# stats_comparator
This app provides stats for GitHub repos which can be compared.

## Running
This is a Symfony & docker-compose based project.

1. `cp .env.dist .env`
1. Fill in your GitHub API key
1. `docker-compose up --build -d`
1. Go to http://localhost:8001.

## Endpoints
This app exposes a RESTful API for making web requests and serves responses in JSON.

1. `GET /stats?repo=user/repo`

Returns 200 with repo data on success:
```json
{
  "forkCount": 54,
  "starCount": 544,
  "watcherCount": 544,
  "openPullRequestCount": 0,
  "closedPullRequestCount": 30,
  "latestReleaseDate": null,
  "name": "amphp/http-client"
}
```

Returns 404 when repo is not found or 400 when bad repo data was provided.

2. `GET /stats/compare?repos=user/repo,user2/repo2,user3/repo3,...`

Returns 200 with repo comparison on success:
```json
{
  "groupedForkCount": {
    "amphp/amp": 168,
    "amphp/http-client": 54
  },
  "groupedStarCount": {
    "amphp/amp": 3035,
    "amphp/http-client": 544
  },
  "groupedWatcherCount": {
    "amphp/amp": 3035,
    "amphp/http-client": 544
  },
  "groupedOpenPullRequestCount": {
    "amphp/amp": 4,
    "amphp/http-client": 0
  },
  "groupedClosedPullRequestCount": {
    "amphp/http-client": 30,
    "amphp/amp": 30
  },
  "groupedLatestReleaseDate": {
    "amphp/http-client": null,
    "amphp/amp": null
  }
}
```

Returns 404 or 400 in cases described above.