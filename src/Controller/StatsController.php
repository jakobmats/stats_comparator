<?php

namespace App\Controller;

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
        $repoQuery = $request->query->filter('repos', '', FILTER_SANITIZE_STRING);
        $parsedQuery = $this->validateRepoName($repoQuery);
        [$userName, $repoName] = $parsedQuery;

        $repo = $this->repoStatsService->findRepo($userName, $repoName);
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
        $repoDataQuery = $request->query->filter('repos', '', FILTER_SANITIZE_STRING);
        $repoData = explode(',', $repoDataQuery);
        $repoNames = array_map(fn ($s) => $this->validateRepoName($s), $repoData);

        if (count($repoNames) <= 1) {
            throw new BadRequestHttpException('At least 2 repos needed for comparison');
        }

        $repos = array_map(fn ($repoName) => $this->repoStatsService->findRepo(...$repoName), $repoNames);
        $stats = array_map(fn ($repo) => $this->repoStatsService->getRepoStats($repo), $repos);

        $statsComparison = $this->repoStatsComparisonService->compare(...$stats);

        return $this->json($statsComparison);
    }

    private function validateRepoName(string $data): array
    {
        if (preg_match('~^([\w\d_-]+)/([\w\d_-]+)$~', $data, $matches) === 1) {
            $regexGroups = array_slice($matches, 1);

            if (count($regexGroups) !== 2) {
                throw new BadRequestHttpException('User or repo name missing.');
            }

            return array_slice($matches, 1);
        }

        throw new BadRequestHttpException('Invalid repo data provided.');
    }
}