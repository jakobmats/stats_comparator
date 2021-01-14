<?php

namespace App\Tests;

use App\Model\Repo;

trait MockRepoDataTrait
{
    private function getRepoData(string $userName, string $repoName): array
    {
        return [
            'id' => 1296269,
            'name' => $repoName,
            'full_name' => "$userName/$repoName",
            'forks_count' => random_int(0, 100),
            'stargazers_count' => random_int(0, 100),
            'watchers_count' => random_int(0, 100),
        ];
    }

    private function getMockRepo(string $userName, string $repoName): Repo
    {
        return new Repo($userName, $repoName, $this->getRepoData($userName, $repoName));
    }

    private function getPullRequestData(int $count, $state = 'open'): array
    {
        return array_fill(0, $count, ['state'=> $state]);
    }

    private function getReleaseData(): array
    {
        return [
            [
                'published_at' => '2013-02-27T19:35:32Z'
            ]
        ];
    }

    private function getWrongReleaseData(): array
    {
        return [
            [
                'published_at' => '2013-02-27X19:35:32Z'
            ]
        ];
    }
}