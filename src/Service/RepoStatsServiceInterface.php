<?php

namespace App\Service;

use App\Model\RepoInterface;
use App\Model\RepoStatsInterface;
use DateTime;

/**
 * Retrieves data associated to VCS repositories
 *
 * @package App\Service
 */
interface RepoStatsServiceInterface
{

    /**
     * Fetches repo stats
     *
     * @param RepoInterface $repo
     * @return RepoStatsInterface
     */
    public function getRepoStats(RepoInterface $repo): RepoStatsInterface;

    /**
     * Locates repo and fetches its data
     *
     * @param string $userName
     * @param string $repoName
     * @return RepoStatsInterface|null
     */
    public function findRepo(string $userName, string $repoName): ?RepoInterface;

    /**
     * @param RepoInterface $repo
     * @return DateTime|null
     */
    public function getLatestReleaseDate(RepoInterface $repo): ?DateTime;

    /**
     * @param RepoInterface $repo
     * @param string $state
     * @return int
     */
    public function getPullRequestCount(RepoInterface $repo, string $state = 'open'): int;

    /**
     * @param RepoInterface $repo
     * @return array
     */
    public function getLatestRelease(RepoInterface $repo): array;
}