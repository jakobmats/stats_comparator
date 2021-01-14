<?php

namespace App\Model;

use DateTime;

class RepoStats implements RepoStatsInterface
{
    private int $forkCount;
    private int $starCount;
    private int $watcherCount;
    private int $openPullRequestCount;
    private int $closedPullRequestCount;
    private ?DateTime $latestReleaseDate;
    private string $name;

    public function __construct(
        int $forkCount,
        int $starCount,
        int $watcherCount,
        int $openPullRequestCount,
        int $closedPullRequestCount,
        ?DateTime $latestReleaseDate,
        string $name
    )
    {
        $this->forkCount = $forkCount;
        $this->starCount = $starCount;
        $this->watcherCount = $watcherCount;
        $this->openPullRequestCount = $openPullRequestCount;
        $this->closedPullRequestCount = $closedPullRequestCount;
        $this->latestReleaseDate = $latestReleaseDate;
        $this->name = $name;
    }

    public function getForkCount(): int
    {
        return $this->forkCount;
    }

    public function getStarCount(): int
    {
        return $this->starCount;
    }

    public function getWatcherCount(): int
    {
        return $this->watcherCount;
    }

    public function getOpenPullRequestCount(): int
    {
        return $this->openPullRequestCount;
    }

    public function getClosedPullRequestCount(): int
    {
        return $this->closedPullRequestCount;
    }

    public function getLatestReleaseDate(): ?DateTime
    {
        return $this->latestReleaseDate;
    }

    public function getName(): string
    {
        return $this->name;
    }
}