<?php

namespace App\Model;

use DateTime;

/**
 * Represents repo stats
 */
interface RepoStatsInterface
{

    /**
     *
     * @return int
     */
    public function getForkCount(): int;

    /**
     * @return int
     */
    public function getStarCount(): int;

    /**
     * @return int
     */
    public function getWatcherCount(): int;

    /**
     * @return int
     */
    public function getOpenPullRequestCount(): int;

    /**
     * @return int
     */
    public function getClosedPullRequestCount(): int;

    /**
     * Returns latest release date or null if there are no releases.
     *
     * @return DateTime|null
     */
    public function getLatestReleaseDate(): ?DateTime;

    /**
     * Returns full repo name
     *
     * @return string
     */
    public function getName(): string;
}