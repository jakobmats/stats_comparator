<?php

namespace App\Service;

use App\Model\RepoStats;
use App\Model\RepoStatsComparison;
use App\Model\RepoStatsComparisonInterface;
use App\Model\RepoStatsInterface;

class RepoStatsComparisonService implements RepoStatsComparisonServiceInterface
{
    public function compare(RepoStatsInterface ...$repoStats): RepoStatsComparisonInterface
    {
        return new RepoStatsComparison(
            $this->compareProperties(fn (RepoStats $repoStats) => $repoStats->getForkCount(), ...$repoStats),
            $this->compareProperties(fn (RepoStats $repoStats) => $repoStats->getStarCount(), ...$repoStats),
            $this->compareProperties(fn (RepoStats $repoStats) => $repoStats->getWatcherCount(), ...$repoStats),
            $this->compareProperties(fn (RepoStats $repoStats) => $repoStats->getOpenPullRequestCount(), ...$repoStats),
            $this->compareProperties(fn (RepoStats $repoStats) => $repoStats->getClosedPullRequestCount(), ...$repoStats),
            $this->compareProperties(fn (RepoStats $repoStats) => $repoStats->getLatestReleaseDate(), ...$repoStats)
        );
    }

    private function compareProperties(callable $getter, RepoStatsInterface ...$repoStatsGroup): array
    {

        // Group and sort repo stats together with respective repo names
        $values = array_merge(...array_map(fn (RepoStats $repoStats) => [$repoStats->getName() => $getter($repoStats)], $repoStatsGroup));
        arsort($values);

        return $values;
    }
}
