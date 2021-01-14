<?php

namespace App\Tests;

use App\Model\RepoStatsComparison;
use App\Model\RepoStatsComparisonInterface;

trait MockComparisonDataTrait
{
    public function getMockRepoStatsComparison(): RepoStatsComparisonInterface
    {
    }
}