## Event Sourcing Example with Laravel

This project is a simple implementation (just for example) of Event Sourcing using the Laravel framework. It demonstrates how to capture and store domain events to reconstruct the state of an application.

> Check claude.md file for detailed technical description.

## Overview

> This is an example made for the talk: https://joind.in/event/longhorn-php-2025/building-resilient-php-applications-with-an-event-driven-mindset
>
> There you'll find the slides, and the conference will have the video published later on - where the overall explanation of this project and its event source principles can be properly found.

Event Sourcing is a design pattern where state changes are logged as a sequence of events. Instead of storing the current state of an entity, we store all the events that led to that state.

### Prerequisites

- PHP >= 8.2
- Composer
- Docker
- Docker Compose

### Installation

This is mentioning docker compose setup for the project. If you want bare metal just make sure all dependencies are in place (including docker compose environment dependencies like redis and php extensions inside the container - visible in the `Dockerfile`).

1. Make a copy of the `.env.example` file and name it `.env`. Adjust any necessary environment variables.

2. Run the artisan commands:

   ```bash
   php artisan key:generate
   php artisan optimize:clear
   ```

3. From inside the `app` directory, install npm dependencies and build assets:

   ```bash
   npm install
   npm run build
   ```

4. Run docker-compose to set up the environment:

   ```bash
   docker compose up -d
   ```

5. Install the PHP dependencies:

   ```bash
   docker compose exec php composer install
   ```

6. Run the migrations to set up the database:

   ```bash
   docker compose exec php php artisan migrate
   ```

After that, just visit `http://localhost:8080` in your browser.
