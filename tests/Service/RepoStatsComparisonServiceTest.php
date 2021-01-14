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
        $first = new RepoStats(
            1,
            2,
            3,
            4,
            5,
            new DateTime(),
            'foo/bar'
        );
        $second = new RepoStats(
            10,
            20,
            30,
            40,
            50,
            new DateTime('yesterday'),
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