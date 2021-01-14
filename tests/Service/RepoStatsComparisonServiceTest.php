<?php

namespace App\Tests\Service;

use App\Model\RepoStats;
use App\Service\RepoStatsComparisonService;
use App\Tests\MockRepoDataTrait;
use DateTime;
use PHPUnit\Framework\TestCase;

class RepoStatsComparisonServiceTest extends TestCase
{
    use MockRepoDataTrait;

    public function testCompareTwoRepos()
    {
        $yesterdayDate = new DateTime('yesterday');
        $date = new DateTime;

        $first = new RepoStats(
            1,
            2,
            3,
            4,
            5,
            $yesterdayDate,
            'foo/bar'
        );

        $second = new RepoStats(
            10,
            20,
            30,
            40,
            50,
            $date,
            'bacon/cheese'
        );

        $comparisonService = new RepoStatsComparisonService();
        $result = $comparisonService->compare($first, $second);

        $this->assertEquals([
            'bacon/cheese' => 10,
            'foo/bar' => 1
        ], $result->getGroupedForkCount());

        $this->assertEquals([
            'bacon/cheese' => 20,
            'foo/bar' => 2,
        ], $result->getGroupedStarCount());

        $this->assertEquals([
            'bacon/cheese' => 30,
            'foo/bar' => 3,
        ], $result->getGroupedWatcherCount());

        $this->assertEquals([
            'bacon/cheese' => 40,
            'foo/bar' => 4,
        ], $result->getGroupedOpenPullRequestCount());

        $this->assertEquals([
            'bacon/cheese' => 50,
            'foo/bar' => 5,
        ], $result->getGroupedClosedPullRequestCount());

        $this->assertEquals([
            'bacon/cheese' => $date,
            'foo/bar' => $yesterdayDate,
        ], $result->getGroupedLatestReleaseDate());
    }

    public function testMissingDate()
    {
        $date = new DateTime;
        $first = new RepoStats(
            1,
            2,
            3,
            4,
            5,
            $date,
            'foo/bar'
        );
        $second = new RepoStats(
            10,
            20,
            30,
            40,
            50,
            null,
            'bacon/cheese'
        );

        $comparisonService = new RepoStatsComparisonService();
        $result = $comparisonService->compare($first, $second);

        $this->assertEquals([
            'foo/bar' => $date,
            'bacon/cheese' => null
        ], $result->getGroupedLatestReleaseDate());
    }
}