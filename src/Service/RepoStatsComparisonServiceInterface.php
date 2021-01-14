<?php

namespace App\Service;

use App\Model\RepoStatsComparisonInterface;
use App\Model\RepoStatsInterface;

/**
 * Handles repo stats comparison
 *
 * @package App\Service
 */
interface RepoStatsComparisonServiceInterface
{

    /**
     * Compares a number of repos and sorts their properties by their values
     *
     * @param RepoStatsInterface ...$repoStatsGroup
     * @return RepoStatsComparisonInterface
     */
    public function compare(RepoStatsInterface ...$repoStatsGroup): RepoStatsComparisonInterface;
}