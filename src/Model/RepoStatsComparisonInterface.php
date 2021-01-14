<?php

namespace App\Model;

use DateTime;

/**
 * Represents stats comparison of two or more repos
 *
 * @package App\Model
 */
interface RepoStatsComparisonInterface
{

    /**
     * @return array
     */
    public function getGroupedForkCount(): array;

    /**
     * @return array
     */
    public function getGroupedStarCount(): array;

    /**
     * @return array
     */
    public function getGroupedWatcherCount(): array;

    /**
     * @return array
     */
    public function getGroupedOpenPullRequestCount(): array;

    /**
     * @return array
     */
    public function getGroupedClosedPullRequestCount(): array;

    /**
     * @return array
     */
    public function getGroupedLatestReleaseDate(): array;
}