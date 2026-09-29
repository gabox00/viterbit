# AGENTS.md

Guide for AI agents working on this repository. Read it before changing anything. For the full human-facing documentation, see `README.md`.

## What this is

A small **applicant tracking system** (ATS):
- Candidates apply to open **job positions** by pasting their CV as plain text.
- Recruiters list, filter and search the applications.
- Each CV is **enriched asynchronously** by a (mocked) LLM, which writes a short summary and a 0–100 relevance score against the position's required skills.

It is a **modular monolith**:
- A Symfony API with DDD, hexagonal architecture and CQRS with domain events.
- A React SPA.
- Everything runs in Docker.

## Stack

| Area | Technology |
|---|---|
| Backend | PHP 8.5, Symfony 8.1, served by FrankenPHP (worker mode) |
| Persistence | PostgreSQL 18, Doctrine ORM 3 with attribute mapping, hand-written SQL migrations |
| Messaging | RabbitMQ 4 + Symfony Messenger (AMQP) |
| Frontend | React 19, React Router 8, TypeScript 7, Vite 8, Tailwind CSS 4 |
| Backend tests | PHPUnit 13, Infection (mutation) |
| Frontend tests | Vitest 5 in browser mode (Chromium), Playwright (e2e) |
| Quality | PHPStan level 9, deptrac, php-cs-fixer, ESLint (strict type-checked) |
| JS package manager | **pnpm only, never npm** |

## Running it

The host only needs **Docker (Compose v2), Make and git**. PHP, Node, pnpm, PostgreSQL, RabbitMQ and the test browsers all live in containers. **Never run `php`, `composer`, `pnpm` or `node` on the host. Go through `make` or `docker compose exec`.**

```bash
make init        # creates backend/.env, builds images, installs deps, starts the stack, migrates, enables git hooks
```

| URL | What |
|---|---|
| http://localhost:5173 | Web app |
| http://localhost:8000/api/health | API |
| http://localhost:15672 | RabbitMQ UI (`viterbit_admin` / `viterbit_admin_password`) |
| `localhost:5432` | PostgreSQL (`viterbit` / `viterbit_password`, db `viterbit`, tests `viterbit_test`) |

Docker services: `php` (API), `worker-enrich-cv-on-job-application-submitted`, `postgres`, `rabbitmq`, `frontend` (Vite dev server), `playwright` (only for tests, profile `test`).

## Commands

| Command | Does |
|---|---|
| `make up` / `make down` / `make restart` | Start, stop or restart the stack |
| `make sh [c=<service>]` / `make logs [c=<service>]` | Shell or logs (default service `php`) |
| `make db-migrate` | Create the databases if missing and migrate (dev and test) |
| `make db-reset` | Drop both databases and migrate again |
| `make test` | All suites except mutation |
| `make test <suite>` | One suite: `unit`, `integration`, `functional`, `smoke`, `e2e`, `mutation`, `front`, `front-e2e` |
| `make lint` | php-cs-fixer (dry run), `doctrine:schema:validate --skip-sync`, ESLint and tsc |
| `make fix` | Auto-fix code style (php-cs-fixer, ESLint) |
| `make stan` / `make deptrac` | PHPStan / architecture rules |
| `make check` | **Everything:** lint, stan, deptrac, all tests and mutation. Run it before declaring a task done |

Git hooks in `.githooks/`, enabled by `make install` with `git config core.hooksPath .githooks`:
- `pre-commit` runs `make lint stan deptrac`.
- `pre-push` runs `make test`.

Both need the stack up.

## Repository layout

```
.
├── backend/                 Symfony app (mounted at /app in the php containers)
│   ├── src/
│   │   ├── Shared/          Shared kernel: buses, Criteria, AggregateRoot, Uuid, LLM port + clients, Doctrine types
│   │   ├── JobPosition/     Read-only catalog of open positions
│   │   └── JobApplication/  Submit, search, detail, and asynchronous CV enrichment
│   ├── config/              services.yaml, packages/{doctrine,messenger,…}.yaml
│   ├── migrations/          Plain SQL migrations (the source of truth for the schema)
│   └── tests/               Unit, Integration, Functional, Smoke, E2e, Mother, Double
├── frontend/src/            api/, components/ui/, features/<screen>/, hooks/, pages/, types/, test/
├── docker/                  php/ (Dockerfile, Caddyfile, php.ini), frontend/, playwright/, rabbitmq/definitions.json
├── .githooks/               pre-commit, pre-push
├── docker-compose.yml
└── Makefile
```

## Backend architecture

### Layers (enforced by `deptrac.layers.yaml`)

Every bounded context has `Domain/`, `Application/` and `Infrastructure/`:

| Layer | Contains | May depend on |
|---|---|---|
| `Domain` | Entities (aggregate roots), value objects, enums, domain events, repository interfaces, exceptions | Domain, **plus `Doctrine\ORM\Mapping` attributes only** |
| `Application` | Commands, queries, handlers, workers, services, DTOs, ports, exceptions | Domain, PSR interfaces |
| `Infrastructure` | Controllers, request DTOs, view models, Doctrine repositories, custom DBAL types, adapters | Everything, including the framework |

Inside each layer, **folders are grouped by type, never by use case**:
- `Domain/{Entity,ValueObject,Enum,Event,Repository,Exception}`
- `Application/{Command,Query,Handler,Dto,Worker,Service,Port,Exception}`
- `Infrastructure/{Http/{Controller,Dto,ViewModel},Persistence/{,Type}}`

### Bounded contexts (enforced by `deptrac.contexts.yaml`)

- A context may use `Shared` and the **published contract** of another context: its domain events and its application queries/DTOs.
- **One exception:** `JobApplication` may use `JobPositionAggregate`, meaning the `JobPosition` entity, value objects and repository, because `JobApplication` has a Doctrine `ManyToOne` to `JobPosition`.

### Request flow (CQRS)

```
Controller (Infrastructure/Http) → Command/Query → bus → Handler (Application) → Domain + repository
                                                                              → ViewModel (JSON, snake_case)
```

- **Buses:** `ICommandBus` (commands return nothing), `IQueryBus` (queries return DTOs) and `IEventBus`, all backed by Messenger.
- **Transactions:** the command bus wraps every command in a Doctrine transaction (`doctrine_transaction` middleware). If publishing the events fails, the save is rolled back.
- **Handler registration:** handlers are registered automatically by implementing `ICommandHandler`/`IQueryHandler` (the `_instanceof` block in `services.yaml`).
- **Identity is generated by the caller:** the controller generates the UUID and passes it in the command, because commands return nothing.

### Asynchronous enrichment

```
POST /api/v1/job-applications → JobApplication::submit() records JobApplicationSubmitted
  → handler saves, then publishes (same transaction)
  → RabbitMQ queue viterbit.job_application.1.enrich_cv_on_job_application_submitted
  → EnrichCvOnJobApplicationSubmittedWorker: load application → CvEnricher (ILlmClient) → attachEnrichment() → save
  → front polls GET /api/v1/job-applications/{hash} every 3 s while status is "received"
```

- **Retries and dead letters:** 3 retries with exponential backoff (1 s, 2 s, 4 s), then the message goes to `….dead_letter`. Inspect with `bin/console messenger:failed:show --transport=enrich_cv_on_job_application_submitted_dead_letter`.
- **Worker registration:** workers are declared by hand in `services.yaml` with `from_transport`, so each one only handles its own queue.
- **Topology:** the RabbitMQ topology (vhost `viterbit`, exchanges, queues, users) lives in `docker/rabbitmq/definitions.json`, and the app never creates it (`auto_setup: false`).
- **LLM:** the LLM port `ILlmClient` and its clients live in `Shared`, and `LlmClientBuilder` picks the provider from `LLM_PROVIDER` (default `mock`). The mock is deterministic:
  - summary = the leading sentences of the CV that fit in 280 characters;
  - score = the percentage of required skills mentioned in the CV.

### Persistence

- **Ids:** every table has `id INTEGER GENERATED BY DEFAULT AS IDENTITY` (primary key; **every FK and relation uses it**, even across contexts) plus `hash UUID UNIQUE`.
  - The **hash is the public identity**: the domain uses it, repositories look up by it (`findByHash`), and the API only ever exposes it (`hash`, `job_position_hash`, `/{hash}` routes).
  - **The integer `id` never leaves persistence.**
- **Mapping:** entities are mapped with `#[ORM\...]` attributes.
- **Value objects** are stored through custom DBAL types in `Infrastructure/Persistence/Type/` (`job_application_hash`, `job_position_hash`, `email`, `ai_score`, `utc_datetime_immutable`). Entities reference the type **by name string**, so the domain never imports infrastructure.
- **Schema:** it comes from `migrations/`, not from `doctrine:schema:update`. The "schema not in sync" message from a full `schema:validate` is expected declaration noise. Only `--skip-sync` is enforced.
- **Test database:** tests use `viterbit_test` (`dbname_suffix` under `when@test`). The test bootstrap creates and migrates it.
- **Search** uses `DoctrineCriteriaConverter`: generic `Criteria`/`Filter`/`Order` from `Shared/Domain`, translated to DQL, with case-insensitive `LOWER(…) LIKE LOWER(…)` and escaped wildcards.

## API

All JSON is `snake_case`. Business routes are versioned under `/api/v1`; infrastructure routes (`/api/health`) are not.

| Method | Path | Notes |
|---|---|---|
| `GET` | `/api/health` | Liveness |
| `GET` | `/api/v1/job-positions` | List positions |
| `GET` | `/api/v1/job-positions/{hash}` | One position |
| `POST` | `/api/v1/job-applications` | Submit; `201 { "hash": … }` |
| `GET` | `/api/v1/job-applications` | Search: `status`, `job_position_hash`, `search`, `page`, `per_page` |
| `GET` | `/api/v1/job-applications/{hash}` | Full detail |

Errors:
- `422 { "error", "errors": { field: message } }` for validation errors;
- `422 { "error" }` for an unknown position;
- `404 { "error" }` for a missing resource.

`ApiExceptionListener` maps domain exceptions: `NotFoundException` → 404, `InvalidValueException` → 422.

## Frontend

- **`api/`:** HTTP client and one module per resource. The DTO types in `types/` mirror the API JSON (`snake_case`).
- **`hooks/`:** data fetching with a small `useQuery`, with polling while an application is being enriched.
- **`components/ui/`:** generic UI (`Card`, badges, skeletons…). **`features/<screen>/`:** screen-specific pieces. **`pages/`:** composition.
- **Component tests** run in real Chromium with the real Tailwind CSS (`src/test/setup.ts`), so layout checks such as overflow are meaningful.
- **User-facing copy** says "position" or "positions". Code identifiers say `jobPosition`.

## Conventions

| Element | Convention | Example |
|---|---|---|
| Interfaces (PHP and TS) | `I` prefix | `IJobApplicationRepository`, `IJobPositionDto` |
| Enums | `Enum` suffix | `JobApplicationStatusEnum` |
| DTOs | `Dto` suffix | `JobApplicationDetailDto` |
| HTTP responses | `ViewModel` suffix | `JobApplicationDetailViewModel` |
| Async consumers | `Worker`, never "Job", named after their queue | `EnrichCvOnJobApplicationSubmittedWorker` |
| Doctrine types | `…Type` | `EmailType` |
| Domain methods | Business verbs, not technical ones | `JobApplication::submit()`, `attachEnrichment()` (never `build`/`create`/setters) |
| Events | `{company}.{service}.{version}.event.{entity}.{action}` | `viterbit.job_application.1.event.job_application.submitted` |
| Queues | `{company}.{service}.{version}.{action}_on_{event}` | `viterbit.job_application.1.enrich_cv_on_job_application_submitted` |
| Queue / transport / worker / Docker service | Same name everywhere | `enrich_cv_on_job_application_submitted` ↔ `worker-enrich-cv-on-job-application-submitted` |
| Tables and columns | Singular, English, `snake_case` | `job_application.applied_at` |
| JSON | `snake_case`; PHP and TS identifiers `camelCase` | `candidate_full_name` / `candidateFullName` |

Every PHP file starts with `declare(strict_types=1);`. Classes are `final` (and `readonly` where possible), unless they are designed to be extended (`AggregateRoot`, `Uuid`, `InvalidValueException`).

## How to add things

**A new endpoint:**
1. Command or query + handler in `Application/`.
2. Controller, request DTO and view model in `Infrastructure/Http/`, with `#[Route('/api/v1/…')]`.
3. A functional test in `tests/Functional/`.

**A new entity:**
1. Entity with `#[ORM\...]` attributes in `Domain/Entity/`.
2. Value objects plus their DBAL types, registered in `config/packages/doctrine.yaml`.
3. A mapping entry in `doctrine.yaml` if it is a new context.
4. A SQL migration with `id` + `hash`.
5. Repository interface in `Domain/Repository/`, `Doctrine*Repository` in `Infrastructure/Persistence/`.
6. An integration test.

**A new reaction to an event:** add it in these places, all with the same name:
1. The queue and its dead letter queue in `docker/rabbitmq/definitions.json`.
2. The transport and its failure transport in `messenger.yaml`.
3. The worker class in `Application/Worker/`, with its `from_transport` tag in `services.yaml`.
4. A Docker service `<<: *worker` with `messenger:consume <transport>`.
5. A route in `messenger.yaml` → `routing`. Recreate RabbitMQ (`docker compose up -d --force-recreate rabbitmq`) so it loads the new definitions.

**A new LLM provider:**
1. A client implementing `ILlmClient` in `Shared/Infrastructure/Llm/`.
2. A case in `LlmProviderEnum`.
3. A branch in `LlmClientBuilder`.

**A new env var:**
1. Add it to `backend/.env.example` too; `backend/.env` is git-ignored.
2. Give optional ones a default under `parameters: env(NAME): …` in `services.yaml`.

## Gotchas

- **Code changes:** the API reloads on PHP and YAML changes by itself (FrankenPHP `watch`), but the **worker is a long-running process**. Restart it after changing code it runs: `docker compose restart worker-enrich-cv-on-job-application-submitted`.
- **Changed migrations:** if you edit an existing migration instead of adding one, run `make db-reset`.
- **Mutation testing:** `pcov` is off in `php.ini` and only turned on for mutation testing (Infection's `initialTestsPhpOptions`). ORM attribute lines are ignored by Infection on purpose.
- **Lazy relations:** `findByHash()` returns a `JobApplication` whose `jobPosition` is a lazy object. `assertEquals` on whole entities does not initialize it, so compare through DTOs, as the repository tests do.
- **Never commit** `backend/.env`, `.claude/` or `.idea/`, and never commit a `package-lock.json`.
- **Don't reintroduce removed things** without being asked: SQLite, husky, a separate Enrichment context, FKs on `hash`, npm.

## Definition of done

1. `make check` passes, and you report the real output, not an assumption.
2. New logic has the smallest test that would fail if it broke.
3. `README.md` is updated when commands, architecture, the API or decisions change.
