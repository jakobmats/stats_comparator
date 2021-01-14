<?php

namespace App\Service;

use App\Exception\RepoNotFoundException;
use App\Model\Repo;
use App\Model\RepoInterface;
use App\Model\RepoStats;
use App\Model\RepoStatsInterface;
use DateTime;
use Exception;
use Symfony\Component\HttpClient\Exception\ClientException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class RepoStatsService implements RepoStatsServiceInterface
{
    private HttpClientInterface $client;

    public function __construct(HttpClientInterface $githubClient)
    {
        $this->client = $githubClient;
    }

    /**
     * @inheritDoc
     */
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

    /**
     * @inheritDoc
     */
    public function findRepo(string $userName, string $repoName): ?RepoInterface
    {
        try {
            $response = $this->client->request('GET', "https://api.github.com/repos/$userName/$repoName");

            return new Repo($userName, $repoName, $response->toArray());
        } catch (ClientException $exception) {

            // Repo was not found
            if ($exception->getResponse()->getStatusCode() === Response::HTTP_NOT_FOUND) {
                return null;
            }

            throw $exception;
        }
    }

    /**
     * @inheritDoc
     */
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

    /**
     * @inheritDoc
     */
    public function getPullRequestCount(RepoInterface $repo, string $state = 'open'): int
    {
        $userName = $repo->getUserName();
        $repoName = $repo->getName();

        try {
            $response = $this->client->request('GET', "https://api.github.com/repos/$userName/$repoName/pulls?state=$state");

            // TODO: Pagination - max pull request count is 30 per page
            return count($response->toArray());
        } catch (ClientException $exception) {


            // No pull requests found
            if ($exception->getResponse()->getStatusCode() === Response::HTTP_NOT_FOUND) {
                return 0;
            }

            throw $exception;
        }
    }

    /**
     * @inheritDoc
     */
    public function getLatestRelease(RepoInterface $repo): array
    {
        $userName = $repo->getUserName();
        $repoName = $repo->getName();

        try {
            $response = $this->client->request('GET', "https://api.github.com/repos/$userName/$repoName/releases/latest");

            return $response->toArray();
        } catch (ClientException $exception) {

            // No releases found
            if ($exception->getResponse()->getStatusCode() === Response::HTTP_NOT_FOUND) {
                return [];
            }

            throw $exception;
        }
    }
}