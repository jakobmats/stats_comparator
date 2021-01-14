<?php

namespace App\Service;

use App\Model\Repo;
use App\Model\RepoInterface;
use App\Model\RepoStats;
use App\Model\RepoStatsInterface;
use DateTime;
use Exception;
use Symfony\Component\Cache\Adapter\AdapterInterface;
use Symfony\Component\HttpClient\Exception\ClientException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class RepoStatsService implements RepoStatsServiceInterface
{
    private HttpClientInterface $client;
    private ?AdapterInterface $cache;

    public function __construct(HttpClientInterface $githubClient, AdapterInterface $cache = null)
    {
        $this->client = $githubClient;
        $this->cache = $cache;
    }

    public function getRepoStats(RepoInterface $repo): RepoStatsInterface
    {
        $dataBag = $repo->getDataBag();
        $latestReleaseDate = $this->getLatestReleaseDate($repo);
        $openPullRequests = $this->getPullRequestCount($repo);
        $closedPullRequests = $this->getPullRequestCount($repo, 'closed');

        return new RepoStats(
            $dataBag['forks_count'],
            $dataBag['stargazers_count'],
            $dataBag['watchers_count'],
            $openPullRequests,
            $closedPullRequests,
            $latestReleaseDate,
            $dataBag['full_name'],
        );
    }

    public function findRepo(string $userName, string $repoName): ?RepoInterface
    {
        try {
            $response = $this->cachedRequest('GET', "https://api.github.com/repos/$userName/$repoName");

            return new Repo($userName, $repoName, $response);
        } catch (ClientException $exception) {

            // Repo was not found
            if ($exception->getResponse()->getStatusCode() === Response::HTTP_NOT_FOUND) {
                throw new NotFoundHttpException('Requested repo ' . "$userName/$repoName" . ' was not found.');
            }

            throw $exception;
        }
    }

    public function getLatestReleaseDate(RepoInterface $repo): ?DateTime
    {
        $releaseData = $this->getLatestRelease($repo);

        if (empty($releaseData) || empty($release['published_at'])) {
            return null;
        }

        try {
            return new DateTime($release['published_at']);
        } catch (Exception $e) {
            return null;
        }
    }

    public function getPullRequestCount(RepoInterface $repo, string $state = 'open'): int
    {
        $userName = $repo->getUserName();
        $repoName = $repo->getName();

        try {
            $response = $this->cachedRequest('GET', "https://api.github.com/repos/$userName/$repoName/pulls?state=$state");

            // TODO: Pagination - max pull request count is 30 per page
            return count($response);
        } catch (ClientException $exception) {


            // No pull requests found
            if ($exception->getResponse()->getStatusCode() === Response::HTTP_NOT_FOUND) {
                return 0;
            }

            throw $exception;
        }
    }

    public function getLatestRelease(RepoInterface $repo): array
    {
        $userName = $repo->getUserName();
        $repoName = $repo->getName();

        try {
            return $this->cachedRequest('GET', "https://api.github.com/repos/$userName/$repoName/releases/latest");
        } catch (ClientException $exception) {

            // No releases found
            if ($exception->getResponse()->getStatusCode() === Response::HTTP_NOT_FOUND) {
                return [];
            }

            throw $exception;
        }
    }

    private function cachedRequest(string $method, string $url): array
    {
        $key = $this->cacheKeyFromUrl($url);

        // No caching
        if ($this->cache === null) {
            return $this->requestArray($method, $url);
        }

        $item = $this->cache->getItem($key);
        if ($item->isHit()) {
            return $item->get();
        }

        $item->set($this->requestArray($method, $url));
        $item->expiresAfter(3600); // Expires after 1h

        $this->cache->save($item);

        return $item->get();
    }

    private function requestArray(string $method, string $url)
    {
        $response = $this->client->request($method, $url);

        return $response->toArray();
    }

    private function cacheKeyFromUrl(string $url)
    {
        return preg_replace('/[^a-zA-Z0-9_.]+/', '_', $url);
    }
}