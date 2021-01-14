<?php

namespace App\Controller;

use App\Model\RepoInterface;
use App\Service\RepoStatsComparisonService;
use App\Service\RepoStatsService;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Annotation\Route;

class StatsController extends AbstractController
{
    private RepoStatsService $repoStatsService;
    private RepoStatsComparisonService $repoStatsComparisonService;

    public function __construct(RepoStatsService $repoStatsService, RepoStatsComparisonService $repoStatsComparisonService)
    {
        $this->repoStatsService = $repoStatsService;
        $this->repoStatsComparisonService = $repoStatsComparisonService;
    }

    /**
     * @Route("/api/stats", methods={"GET"})
     *
     * @param Request $request
     * @return Response
     * @throws Exception
     */
    public function stats(Request $request): Response
    {
        $repoQuery = $request->query->get('repo', '');
        $parsedQuery = $this->parseRepoName($repoQuery);

        if (count($parsedQuery) !== 2) {
            throw new BadRequestHttpException('Invalid owner or repo name.');
        }

        [$userName, $repoName] = $parsedQuery;
        $repo = $this->repoStatsService->findRepo($userName, $repoName);

        if ($repo === null) {
            throw $this->createNotFoundException('Requested repo was not found.');
        }

        $stats = $this->repoStatsService->getRepoStats($repo);

        return $this->json($stats);
    }

    /**
     * @Route("/api/stats/compare", methods={"GET"})
     *
     * @param Request $request
     * @return Response
     * @throws Exception
     */
    public function compareStats(Request $request): Response
    {
        $repoDataQuery = $request->query->get('repos', '');
        $repoData = explode(',', $repoDataQuery);
        $repoNames = array_map(fn ($s) => $this->parseRepoName($s), $repoData);
        $repos = array_map(function ($repoName): RepoInterface {
            $repo = $this->repoStatsService->findRepo(...$repoName);

            if ($repo === null) {
                throw $this->createNotFoundException('Requested repo was not found.');
            }

            return $repo;
        }, $repoNames);

        $stats = array_map(
            fn (RepoInterface $repo) => $this->repoStatsService->getRepoStats($repo),
            $repos
        );

        $statsComparison = $this->repoStatsComparisonService->compare(...$stats);

        return $this->json($statsComparison);
    }

    private function parseRepoName(string $data): array
    {
        if (preg_match('~([\w\d_-]+)/([\w\d_-]+)~', $data, $matches) === 1) {
            return array_slice($matches, 1);
        }

        return [];
    }
}