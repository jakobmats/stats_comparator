# stats_comparator
This app provides *stats* for GitHub repos which can be compared.

## Running
This is a Symfony & docker-compose based project.

1. `cp .env.dist .env`
1. Fill in your GitHub API key
1. `docker-compose up --build -d`
1. Go to http://localhost:8001.

## Usage
This app exposes a RESTful API for making web requests.
It's annotated with OpenAPI and provides swagger-ui at http://localhost:8001/api/doc.