# Health Tracker - Laravel Event Sourcing Project

## Project Overview

This is a Laravel health tracking application built with **event sourcing architecture** using the [Spatie Laravel Event Sourcing](https://github.com/spatie/laravel-event-sourcing) package. The project follows a strict event sourcing implementation with a Command-CommandHandler pattern that sits between controllers and aggregates.

## Project Structure

### Root Directory Layout

- **`app/`** - Contains the entire Laravel application
- **`docker-compose.yml`** - Docker Compose configuration (root level)
- Application exposed at: **http://localhost:8080**

### Application Architecture

The Laravel application is located in the `app/` directory and follows a **Domain-Driven Design (DDD)** structure with event sourcing patterns:

```
app/
├── app/
│   ├── Domains/               # Domain-specific business logic
│   │   ├── AppInstance/
│   │   ├── HealthProfile/
│   │   └── Profile/
│   ├── EventSourcing/         # Event sourcing infrastructure
│   │   └── Interfaces/
│   └── Http/
│       └── Controllers/       # Controllers use CommandHandlers
├── database/
├── resources/
└── composer.json
```

## Event Sourcing Architecture

### Core Components

#### 1. Commands

- **Location**: `app/Domains/{Domain}/Commands/`
- **Purpose**: Plain data transfer objects that represent user intentions
- **Interface**: `App\EventSourcing\Interfaces\CommandInterface`
- **Example**: `CreateProfileCommand`, `StoreHealthMetricCommand`

```php
class CreateProfileCommand implements CommandInterface
{
    public function __construct(
        public string $name,
        public string $role,
        public string $instanceUuid,
        public ?int $userId,
    ) {}
}
```

#### 2. Command Handlers

- **Location**: `app/Domains/{Domain}/CommandHandlers/`
- **Purpose**: Handle commands and interact with aggregates
- **Interface**: `App\EventSourcing\Interfaces\CommandHandlerInterface`
- **Pattern**: Dependency-injected into controllers
- **Example**: `CreateProfileCommandHandler`

```php
class CreateProfileCommandHandler implements CommandHandlerInterface
{
    public function handle(CommandInterface $command): void
    {
        // Validate command type
        // Retrieve or create aggregate
        // Apply business logic
        // Persist aggregate
    }
}
```

#### 3. Aggregates

- **Location**: `app/Domains/{Domain}/Aggregates/`
- **Purpose**: Encapsulate business logic and record events
- **Base**: Extends `Spatie\EventSourcing\AggregateRoots\AggregateRoot`
- **Example**: `ProfileAggregate`, `HealthProfileAggregate`

#### 4. Events

- **Location**: `app/Domains/{Domain}/Events/`
- **Purpose**: Immutable records of things that happened
- **Naming**: Past tense (e.g., `ProfileCreated`, `HealthMetricStored`)

#### 5. Projectors

- **Location**: `app/Domains/{Domain}/Projectors/`
- **Purpose**: Build read models from events
- **Base**: Extends `Spatie\EventSourcing\EventHandlers\Projectors\Projector`
- **Examples**: `ProfileProjector`, `HealthMetricsProjector`

#### 6. Projections

- **Location**: `app/Domains/{Domain}/Projections/`
- **Purpose**: Read-optimized database tables/models
- **Usage**: Used by controllers for queries

#### 7. Reactors

- **Location**: `app/Domains/{Domain}/Reactors/`
- **Purpose**: Handle side effects of events (e.g., sending emails, notifications)
- **Base**: Extends `Spatie\EventSourcing\EventHandlers\Reactors\Reactor`

## Request Flow

The application follows this strict flow pattern:

```
Controller → CommandHandler → Aggregate → Events → Projectors/Reactors
                                                  ↓
                                            Projections (Read Models)
```

### Example Flow

1. **User Request** → Controller receives HTTP request
2. **Command Creation** → Request creates a Command object
3. **Command Handling** → CommandHandler (dependency-injected) processes the command
4. **Aggregate Logic** → CommandHandler retrieves aggregate and applies business rules
5. **Event Recording** → Aggregate records domain events
6. **Persist** → Aggregate persists recorded events to event store
7. **Projectors** → Listen to events and update read models
8. **Reactors** → Listen to events and trigger side effects

### Controller Example

```php
public function store(
    StoreProfileRequest $request,
    CreateProfileCommandHandler $createCommandHandler,
    UpdateProfileCommandHandler $updateCommandHandler,
): RedirectResponse {
    $command = $request->getCommand();

    if ($request->profile === null) {
        $createCommandHandler->handle($command);
        $message = 'Profile created successfully';
    } else {
        $updateCommandHandler->handle($command);
        $message = 'Profile updated successfully';
    }

    return redirect()
        ->route('profiles.index')
        ->with('success', $message);
}
```

## Domain Organization

Each domain follows a consistent structure:

```
Domains/{DomainName}/
├── Aggregates/           # Business logic and event recording
├── Commands/             # Command DTOs
├── CommandHandlers/      # Command processing logic
├── Events/               # Domain events
├── Projectors/           # Read model builders
├── Projections/          # Read models (Eloquent models)
└── Reactors/             # Side effect handlers
```

### Current Domains

1. **AppInstance** - Application instance management
2. **Profile** - User profile management
3. **HealthProfile** - Health metrics tracking

## Docker Environment

### Services

- **PHP Application**: `Dockerfile`
  - Container: `app`
  - Port: `8080:8080`
  - Volume: `./app` → `/var/www/html`

- **Redis**: `redis:7`
  - Container: `redis`
  - Port: `6379:6379`
  - Purpose: Queue/cache backend

### Running Commands

Execute artisan commands via docker compose:

```bash
docker compose exec php php artisan <command>
docker compose exec php php artisan tinker
```

## Technology Stack

- **Laravel**: 12.x
- **PHP**: 8.2+
- **Event Sourcing**: Spatie Laravel Event Sourcing 7.12
- **Queue Management**: Laravel Horizon
- **Cache/Queue**: Redis
- **Testing**: Pest PHP
- **Static Analysis**: PHPStan, Larastan

## Key Conventions

### Command Pattern

- Commands are simple DTOs implementing `CommandInterface`
- Command handlers implement `CommandHandlerInterface` with a `handle()` method
- Controllers inject command handlers via dependency injection
- Command handlers interact with aggregates, NOT directly with databases

### Event Sourcing Patterns

- All state changes go through aggregates
- Aggregates record events for every state change
- Read models (projections) are built by projectors listening to events
- Use `AggregateRoot::retrieve($uuid)` to load aggregate state
- Call `->persist()` after recording events to commit them

### Naming Conventions

- Commands: `{Verb}{Entity}Command` (e.g., `CreateProfileCommand`)
- Handlers: `{Verb}{Entity}CommandHandler`
- Events: `{Entity}{PastTenseVerb}` (e.g., `ProfileCreated`)
- Aggregates: `{Entity}Aggregate`
- Projectors: `{Entity}Projector`
- Projections: `{Entity}` (singular Eloquent model)

### Code Organization

- Follow DDD principles with bounded contexts (domains)
- Keep business logic in aggregates
- Controllers should be thin, delegating to command handlers
- Use projections for all read operations
- Reactors for side effects only

## Development Workflow

### Adding New Features

When adding new features, follow this pattern:

1. Create a **Command** in `Domains/{Domain}/Commands/`
2. Create a **CommandHandler** in `Domains/{Domain}/CommandHandlers/`
3. Create/update **Events** in `Domains/{Domain}/Events/`
4. Update the **Aggregate** in `Domains/{Domain}/Aggregates/`
5. Create/update **Projector** in `Domains/{Domain}/Projectors/`
6. Create/update **Projection** model in `Domains/{Domain}/Projections/`
7. Inject **CommandHandler** into controller and call `handle()`

### Testing Strategy

- Test aggregates by recording and asserting events
- Test command handlers with integration tests
- Test projectors by dispatching events and checking projections
- Use Pest PHP for all tests

## Important Notes

- **Never bypass aggregates**: All write operations must go through aggregates
- **Immutable events**: Events are immutable historical records
- **Eventual consistency**: Read models are eventually consistent with event store
- **Event store**: Primary source of truth for all data
- **Command-Query Separation**: Commands (write) vs Projections (read)
