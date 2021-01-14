<?php

namespace App\Tests\Service;

use App\Model\Repo;
use App\Model\RepoInterface;
use App\Service\RepoStatsService;
use App\Tests\MockRepoDataTrait;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;
use Symfony\Component\HttpFoundation\Response;

class RepoStatsServiceTest extends TestCase
{
    use MockRepoDataTrait;

    private RepoInterface $repo;

    public function setUp(): void
    {
        $this->repo = $this->getMockRepo('foo', 'bar');
    }

    public function testRepoExists()
    {
        $client = new MockHttpClient(new MockResponse(
            json_encode($this->getRepoData('foo', 'bar'))
        ));
        $service = new RepoStatsService($client);
        $repo = $service->findRepo('foo', 'bar');

        $this->assertEquals('foo/bar', $repo->getDataBag()['full_name']);
    }

    public function testRepoDoesNotExist()
    {
        $client = new MockHttpClient(
            new MockResponse('', [
                'http_code' => Response::HTTP_NOT_FOUND
            ])
        );
        $service = new RepoStatsService($client);
        $repo = $service->findRepo('foo', 'bar');

        $this->assertNull($repo);
    }

    public function testReleaseFound()
    {
        $client = new MockHttpClient(new MockResponse(
            json_encode($this->getReleaseData())
        ));
        $service = new RepoStatsService($client);
        $release = $service->getLatestRelease($this->repo);

        $this->assertNotEmpty($release);
    }

    public function testNoReleasesFound()
    {
        $client = new MockHttpClient(new MockResponse(
            json_encode([])
        ));
        $service = new RepoStatsService($client);
        $release = $service->getLatestRelease($this->repo);

        $this->assertEmpty($release);
    }

    public function testReleaseDateInvalid()
    {
        $client = new MockHttpClient(new MockResponse(
            json_encode($this->getWrongReleaseData())
        ));
        $service = new RepoStatsService($client);
        $releaseDate = $service->getLatestReleaseDate($this->repo);

        $this->assertNull($releaseDate);
    }

    public function testPullRequestCount()
    {
        $client = new MockHttpClient([
            new MockResponse(json_encode($this->getPullRequestData(3))),
            new MockResponse(json_encode($this->getPullRequestData(5, 'closed')))
        ]);
        $service = new RepoStatsService($client);

        $open = $service->getPullRequestCount($this->repo);
        $closed = $service->getPullRequestCount($this->repo, 'closed');

        $this->assertEquals(3, $open);
        $this->assertEquals(5, $closed);
    }


}