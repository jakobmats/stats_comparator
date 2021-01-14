<?php

namespace App\Model;

use DateTime;

class RepoStatsComparison implements RepoStatsComparisonInterface
{
    private array $groupedForkCount;
    private array $groupedStarCount;
    private array $groupedWatcherCount;
    private array $groupedOpenPullRequestCount;
    private array $groupedClosedPullRequestCount;
    private array $groupedLatestReleaseDate;

    public function __construct(
        $groupedForkCount,
        $groupedStarCount,
        $groupedWatcherCount,
        $groupedOpenPullRequestCount,
        $groupedClosedPullRequestCount,
        $groupedLatestReleaseDate
    )
    {
        $this->groupedForkCount = $groupedForkCount;
        $this->groupedStarCount = $groupedStarCount;
        $this->groupedWatcherCount = $groupedWatcherCount;
        $this->groupedOpenPullRequestCount = $groupedOpenPullRequestCount;
        $this->groupedClosedPullRequestCount = $groupedClosedPullRequestCount;
        $this->groupedLatestReleaseDate = $groupedLatestReleaseDate;
    }

    public function getGroupedForkCount(): array
    {
        return $this->groupedForkCount;
    }

    public function getGroupedStarCount(): array
    {
        return $this->groupedStarCount;
    }

    public function getGroupedWatcherCount(): array
    {
        return $this->groupedWatcherCount;
    }

    public function getGroupedOpenPullRequestCount(): array
    {
        return $this->groupedOpenPullRequestCount;
    }

    public function getGroupedClosedPullRequestCount(): array
    {
        return $this->groupedClosedPullRequestCount;
    }

    public function getGroupedLatestReleaseDate(): array
    {
        return $this->groupedLatestReleaseDate;
    }
}