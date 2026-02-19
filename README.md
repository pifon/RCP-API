# RCP-API

A strictly **JSON:API v1.1** compliant recipe management backend built with Laravel 11 and Doctrine ORM. Designed as a standalone API to be consumed by a separate frontend (Laravel + Vue.js) or any third-party client.

Production: [pifon.co.uk/api](https://pifon.co.uk/api)

---

## Tech Stack

| Layer | Technology |
|-------|-----------|
| Framework | Laravel 11 (HTTP shell only — business logic is framework-agnostic) |
| ORM | Doctrine ORM 3 via laravel-doctrine/orm |
| Auth | JWT (tymon/jwt-auth 2.x) |
| Database | MariaDB (LTS) |
| Runtime | PHP 8.4, Nginx |
| Containers | Docker Compose (app + database) |
| Documentation | OpenAPI 3.1 YAML served via L5-Swagger UI |
| Testing | PHPUnit 11, PHPStan/Larastan, PHP_CodeSniffer |
| CI/CD | GitHub Actions (lint, analyse, test) |

## Architecture

```
HTTP Layer (Laravel — thin, replaceable)
├── Routes                         api.php + api_v1.php
├── Controllers                    Single-action invokable classes
├── Middleware                      Auth, JSON:API validation, feature gates, rate limiting
└── Exception handler              JSON:API error objects for all HTTP errors

JSON:API Layer (framework-agnostic)
├── Document                       Response envelope builder ({data, included, meta, links, jsonapi})
├── AbstractTransformer            Base class for resource serialisation
├── QueryParameters                Parses ?filter, ?sort, ?page, ?include, ?fields
├── Pagination                     Cursor/offset pagination with JSON:API links
├── SortField / ErrorObject        Value objects

Domain Layer (framework-agnostic)
├── Entities (29)                  Doctrine ORM mapped classes
├── Repositories (10)              Query logic, filtering, pagination
└── Services                       Business logic (FeatureGate)
```

All responses use `Content-Type: application/vnd.api+json` and follow the JSON:API document structure with `jsonapi.version: "1.1"`.

## Features

### Authentication & Users
- JWT login / token refresh
- User registration (JSON:API format)
- Profile retrieval and update (`/me`)
- Password management

### User Preferences
- Spice tolerance setting
- Preferred cuisines (many-to-many)
- Excluded products (many-to-many)
- Dietary restrictions (many-to-many)

### Recipe Management
- Full CRUD for recipes (slug-based URLs)
- Status workflow: draft → published → archived
- Filtering by status, difficulty; sorting by any attribute
- Sparse fieldsets and relationship includes
- Pricing support (price, currency) for paid content
- Fork revenue tracking (fork_revenue_percent)

### Recipe Creation (4 paths)

| Path | Endpoint | Use case |
|------|----------|----------|
| **Simple** | `POST /recipes` | Create recipe shell, add ingredients/directions later |
| **One-shot** | `POST /recipes/full` | Full recipe with ingredients + directions in one request |
| **Step-by-step** | `POST /recipes/{slug}/directions` × N | Build recipe by adding method steps — ingredients auto-extracted |
| **Update** | `PATCH /recipes/{slug}` | Update metadata (title, description, difficulty, status, etc.) — owner-only |
| **Delete** | `DELETE /recipes/{slug}` | Soft-delete a recipe — owner-only |
| **Import** | `POST /recipes/import` | Restore from exported `.recipe.json` file |

### Recipe Preparation (Ingredients & Directions)
- **Ingredients**: ordered list linked to products, measures, and amounts with optional notes
- **Directions**: ordered steps, each linking an operation (action verb), optional duration, optional product/serving, and optional notes
- **Step-by-step auto-linking**: when adding a direction with a product, the system automatically creates or accumulates an ingredient on the recipe and links the direction to it
- **Prep-time auto-calculation**: `prep_time_minutes` is recalculated as the sum of all direction durations on every add/remove
- **Same-product accumulation**: if a later step uses the same product+measure, the existing ingredient's amount is increased rather than creating a duplicate
- **Step injection**: directions can be inserted at a specific position, with automatic renumbering of subsequent steps

### Cuisine Management
- **Cuisine is required** on every recipe (via existing cuisine ID or a cuisine request).
- If a recipe is created with a non-existent cuisine ID, the API returns a `422` with a link to `POST /cuisine-requests`.
- Authors can submit `POST /cuisine-requests` with a name, variant, and description.
- Admins approve (`POST /cuisine-requests/{id}/approve`) which creates the cuisine and automatically upgrades all recipes referencing that request.
- Admins can reject (`POST /cuisine-requests/{id}/reject`) with notes.

### Recipe Export & Import
- **Export** (`GET /recipes/{slug}/export`): produces a portable JSON file containing the full recipe (general info, ingredients, directions) in the `pifon-recipe` v1.0 format — **owner-only** (protected by `recipe-owner` middleware)
- **Import** (`POST /recipes/import`): accepts the export format and creates a brand-new recipe (never updates existing); slug collisions are auto-resolved with suffixes; the importing user becomes the author

### Step-by-Step Workflow (Draft → Publish)
1. `POST /recipes` with a title — creates a `draft` recipe, returns the slug
2. `PATCH /recipes/{slug}` — update metadata (description, difficulty, serves, etc.)
3. `POST /recipes/{slug}/ingredients` / `POST /recipes/{slug}/directions` — build up the recipe
4. `PATCH /recipes/{slug}` with `status: published` — publish (sets `published-at` timestamp)

All calls are stateless; the slug is the only state the client carries between steps. `PATCH` is naturally idempotent.

### Recipe Fork / Clone
- `POST /recipes/{slug}/fork` deep-copies any recipe for the authenticated user
- Clones all ingredients (with notes), all directions (with notes, procedures, servings)
- New author is the forking user; `forkedFrom` links back to the original (1-level only, no chain)
- Fork always starts as `draft`; slug is `{original}-fork` with auto-suffix on collision
- Operations are shared (reusable lookups), but servings and procedures are independent copies

### Ratings & Comments
- Rate any recipe (1-5, upsert per user)
- Threaded comments with parent-child support
- Soft-deleted comment bodies preserved for thread integrity

### User Activity Tracking
- Log actions: viewed, cooked, saved, shared
- Per-user, per-recipe activity history

### Collections (Bags & Menus)
- Two collection types: `bag` (unordered) and `menu` (ordered, with scheduling)
- Full CRUD with soft deletes
- Add/remove recipe items with position, scheduled date, meal slot, and notes
- Plan-based limits enforced via middleware

### Shopping Lists
- Full CRUD for lists and their items
- Status tracking: active, completed, archived
- Items linked to products, measures, and optionally to source recipes
- Check/uncheck items
- Optional auto-add to pantry when checking off

### Pantry Management
- Full CRUD for pantry items with product, quantity, measure
- Expiry and best-before date tracking with computed flags (`is-expired`, `is-past-best-before`)
- Consume endpoint to decrement quantities (auto-removes when depleted)
- Full audit trail via `pantry_logs` (added, consumed, expired, adjusted)
- Plan-based limits enforced via middleware

### Search & Discovery
- **Recipe search** (`GET /recipes/search?q=`): full-text search across recipe titles, descriptions, and ingredient names — ranked by relevance (title=10, description=5, ingredient=3)
- **Autocomplete** (`GET /recipes/search/autocomplete?q=`): fast prefix-based title suggestions with configurable `limit` param (min 2 chars, max 25 results)
- **Product search** (`GET /products/search?q=`): search products by name or slug
- All search endpoints support JSON:API pagination, and recipe search supports `filter[status]`, `filter[difficulty]`, `filter[cuisine]`, `filter[dish-type]`

### "What Can I Cook?" Search
- `GET /v1/pantry/cookable` matches pantry products against recipe ingredients
- Configurable tolerance: `filter[max-missing]=N` for missing ingredient count
- Returns recipes ranked by match percentage with meta: `total-ingredients`, `matched`, `missing`

### Follow System
- Polymorphic follows: follow authors or users
- List followed entities at `/me/following` with type filtering

### Subscription Plans & Monetization
- Three-tier plan catalog: Free, Pro, Premium
- Per-plan feature flags with configurable limits:
  - `max_collections`, `max_shopping_lists`, `max_pantry_items`
  - `paid_recipes` (boolean)
  - `api_rate_limit` (requests/minute)
- Subscribe, view, and cancel subscriptions at `/me/subscription`
- Billing cycle support: monthly and yearly with computed period dates

### Access Control
- **Feature gates** — middleware enforces plan limits on resource creation
- **Paid recipe gate** — blocks access to priced recipes for users without Pro/Premium
- **Author tier enforcement** — middleware validates minimum author tier (free/verified/pro/premium)
- **Recipe owner gate** — middleware restricts actions (e.g. export) to the recipe's author only
- **Tiered rate limiting** — dynamic per-minute limits based on subscription plan (30 anon / 60 free / 300 pro / 1000 premium), with `X-RateLimit-Limit` and `X-RateLimit-Remaining` headers

### Catalog Resources
- Cuisines (hierarchical with parent_id, slug-based)
- Authors (with tier: free/verified/pro/premium)
- Dish types
- Products (with nutrition data, taste profiles, shelf life)
- Measures and measure conversions
- Allergens, sensations, tags, equipment

## API Endpoints

### Public (no auth)

| Method | URI | Description |
|--------|-----|-------------|
| GET | `/api` | API root — name and version |
| POST | `/api/login` | JWT authentication |
| POST | `/api/register` | User registration |

### Authenticated (`/api/v1/...`)

**Me**

| Method | URI | Description |
|--------|-----|-------------|
| GET | `/me` | Current user profile |
| PATCH | `/me` | Update profile |
| GET | `/me/preferences` | Preference settings |
| PATCH | `/me/preferences` | Update preferences |
| GET | `/me/following` | List followed entities |
| GET | `/me/subscription` | Current subscription |
| POST | `/me/subscription` | Subscribe to plan |
| DELETE | `/me/subscription` | Cancel subscription |

**Plans**

| Method | URI | Description |
|--------|-----|-------------|
| GET | `/plans` | List active plans with feature limits |
| GET | `/plans/{slug}` | Plan detail |

**Recipes**

| Method | URI | Description |
|--------|-----|-------------|
| GET | `/recipes` | List (filterable, sortable, paginated) |
| GET | `/recipes/search?q=` | Full-text search (title, description, ingredients) |
| GET | `/recipes/search/autocomplete?q=` | Fast prefix-based title suggestions |
| POST | `/recipes` | Create recipe (shell) |
| POST | `/recipes/full` | Create complete recipe (general + ingredients + directions) |
| POST | `/recipes/import` | Import recipe from exported JSON file |
| GET | `/recipes/{slug}` | Show recipe (paid-recipe gate) |
| PATCH | `/recipes/{slug}` | Update recipe metadata (owner-only) |
| DELETE | `/recipes/{slug}` | Soft-delete recipe (owner-only) |
| GET | `/recipes/{slug}/export` | Export full recipe as JSON (owner-only) |
| POST | `/recipes/{slug}/fork` | Fork/clone recipe for current user |
| GET | `/recipes/{slug}/preparation` | Full recipe view (general + ingredients + directions) |
| GET | `/recipes/{slug}/ingredients` | List ingredients |
| POST | `/recipes/{slug}/ingredients` | Add ingredient |
| DELETE | `/recipes/{slug}/ingredients/{id}` | Remove ingredient |
| GET | `/recipes/{slug}/directions` | List directions |
| POST | `/recipes/{slug}/directions` | Add direction (auto-creates ingredient, recalcs prep-time) |
| DELETE | `/recipes/{slug}/directions/{id}` | Remove direction (renumbers, recalcs prep-time) |
| GET | `/recipes/{slug}/ratings` | List ratings |
| POST | `/recipes/{slug}/ratings` | Rate (upsert) |
| GET | `/recipes/{slug}/comments` | List comments |
| POST | `/recipes/{slug}/comments` | Post comment |
| POST | `/recipes/{slug}/activity` | Log activity |

**Collections**

| Method | URI | Description |
|--------|-----|-------------|
| GET | `/collections` | List user collections |
| POST | `/collections` | Create (plan limit) |
| GET | `/collections/{id}` | Show |
| PATCH | `/collections/{id}` | Update |
| DELETE | `/collections/{id}` | Soft delete |
| POST | `/collections/{id}/items` | Add recipe |
| DELETE | `/collections/{id}/items/{itemId}` | Remove item |

**Shopping Lists**

| Method | URI | Description |
|--------|-----|-------------|
| GET | `/shopping-lists` | List |
| POST | `/shopping-lists` | Create (plan limit) |
| GET | `/shopping-lists/{id}` | Show |
| PATCH | `/shopping-lists/{id}` | Update |
| DELETE | `/shopping-lists/{id}` | Delete |
| GET | `/shopping-lists/{id}/items` | List items |
| POST | `/shopping-lists/{id}/items` | Add item |
| PATCH | `/shopping-lists/{id}/items/{itemId}` | Check/uncheck |
| DELETE | `/shopping-lists/{id}/items/{itemId}` | Remove item |

**Pantry**

| Method | URI | Description |
|--------|-----|-------------|
| GET | `/pantry` | List pantry items |
| POST | `/pantry` | Add item (plan limit) |
| GET | `/pantry/{id}` | Show item |
| PATCH | `/pantry/{id}` | Update item |
| POST | `/pantry/{id}/consume` | Consume quantity |
| DELETE | `/pantry/{id}` | Remove item |
| GET | `/pantry/cookable` | What can I cook? |

**Follows**

| Method | URI | Description |
|--------|-----|-------------|
| POST | `/follows` | Follow author/user |
| DELETE | `/follows/{id}` | Unfollow |

**Products**

| Method | URI | Description |
|--------|-----|-------------|
| GET | `/products/search?q=` | Search products by name or slug |

**Catalog**

| Method | URI | Description |
|--------|-----|-------------|
| GET | `/cuisines` | List cuisines |
| GET | `/cuisines/{slug}` | Show cuisine |
| GET | `/cuisine-requests` | List pending cuisine requests |
| POST | `/cuisine-requests` | Submit a new cuisine request |
| GET | `/cuisine-requests/{id}` | Show cuisine request |
| POST | `/cuisine-requests/{id}/approve` | Approve request (creates cuisine, upgrades recipes) |
| POST | `/cuisine-requests/{id}/reject` | Reject request with admin notes |
| GET | `/authors/{slug}` | Show author |
| GET | `/dish-types/{slug}` | Show dish type |

## Database

57 migrations across 55+ tables covering:

- **Core**: users, recipes, ingredients, ingredient_notes, servings, directions, direction_notes, operations, procedures, measures, products
- **Taxonomy**: cuisines, dish_types, authors, tags, allergens, sensations, equipment
- **Social**: ratings, recipe_comments, follows, user_recipe_activity
- **Organization**: collections, collection_items
- **Shopping**: shopping_lists, shopping_list_items
- **Pantry**: pantry_items, pantry_logs
- **Monetization**: plans, plan_features, user_subscriptions
- **Recipe metadata**: recipe_images, recipe_taste_profiles, recipe_sensations, product_sensations

24 seeders provide reference data for products, measures, cuisines, dish types, authors, recipes, sensations, allergens, tags, equipment, plans, and more.

## Project Structure

```
app/
├── Console/Commands/       PublishDocs (docs:publish artisan command)
├── Documentation/          OpenAPI 3.1 swagger.yaml
├── Entities/               33 Doctrine ORM entities
├── Exceptions/v1/          JSON:API error exceptions (NotFoundException, ValidationError, etc.)
├── Http/
│   ├── Controllers/        AuthController + 64 single-action v1 controllers
│   └── Middleware/          8 middleware (ForceJson, ValidateJsonApi, CheckFeature,
│                            CheckPaidRecipe, CheckAuthorTier, TieredRateLimit, RecipeOwner, Authenticate)
├── JsonApi/                Framework-agnostic JSON:API core (Document, Transformer, Pagination, etc.)
├── Providers/              AppServiceProvider, RouteServiceProvider
├── Repositories/v1/        10 Doctrine repositories
├── Services/               FeatureGate (subscription/plan logic)
└── Transformers/v1/        21 JSON:API resource transformers

database/
├── migrations/             57 migration files
└── seeders/                24 seeder classes

routes/
├── api.php                 Public routes (login, register, root)
└── api_v1.php              Authenticated v1 routes (all features)

tests/
├── Unit/
│   ├── JsonApi/            SortFieldTest, ErrorObjectTest, QueryParametersTest,
│   │                       PaginationTest, DocumentTest, AbstractTransformerTest
│   ├── Entities/           RecipeTest, IngredientTest, DirectionTest, DirectionNoteTest,
│   │                       OperationTest, ProcedureTest, ServingTest, CuisineTest, AuthorTest
│   ├── Middleware/         ForceJsonResponseTest, ValidateJsonApiTest, CheckAuthorTierTest
│   └── Exceptions/         NotFoundExceptionTest, ValidationErrorExceptionTest
├── Feature/
│   ├── AuthenticationTest, JsonApiContractTest
│   └── Http/Controllers/   Recipe (CRUD, ImportExport, StepByStep, RatingsComments),
│                            ShoppingListTest, PantryTest, PlansTest, CatalogTest
├── Helpers/                CreatesTestUser, JsonApiRequests traits
└── Postman/                Pifon.postman_collection.json (61 requests, 11 folders)
```

## Getting Started

### Prerequisites

- Docker and Docker Compose
- PHP 8.4+ (for local development without Docker)
- Composer

### Setup

```bash
git clone https://gitlab.pifon.co.uk/pifon/api.git rcp-api
cd rcp-api
cp .env.example .env

# Start containers
docker compose up -d

# Install dependencies (inside container)
docker exec api composer install

# Run migrations and seed
docker exec api php artisan migrate --seed

# Seed plans (subscription tiers)
docker exec api php artisan db:seed --class=PlansSeeder
```

The API is available at `https://localhost/api`.

### Authentication

```bash
# Login
curl -sk https://localhost/api/login \
  -H "Content-Type: application/vnd.api+json" \
  -d '{"username":"test-user","password":"password"}'

# Use the returned token
curl -sk https://localhost/api/v1/me \
  -H "Authorization: Bearer <token>" \
  -H "Accept: application/vnd.api+json"
```

### Postman

Import `tests/Postman/Pifon.postman_collection.json` into Postman. The collection includes:

- 61 pre-configured requests across 11 folders
- Auto-token extraction on login (saves to `{{token}}` variable)
- Sample JSON:API request bodies for all write endpoints
- Pre-filled query parameters (filters, pagination, includes) ready to toggle

Set the `baseUrl` variable to your host (e.g. `https://localhost/api`).

### API Documentation

Interactive Swagger UI is available at `/api/documentation` and is powered by the OpenAPI 3.1 spec at `app/Documentation/swagger.yaml`.

```bash
# Publish the YAML spec to storage (required after editing swagger.yaml)
docker exec api php artisan docs:publish
```

> **Note**: Do **not** use `l5-swagger:generate` — it expects PHP annotations. The spec is maintained as a hand-written YAML file. Use `docs:publish` to copy it to the location L5-Swagger serves.

## CI / CD

GitHub Actions runs three jobs on every push and PR to `master`:

| Job | Tool | What it checks |
|-----|------|---------------|
| **lint** | PHP_CodeSniffer (PSR-12) | Code style across `app/`, `tests/`, `routes/`, `config/`, `database/seeders/` |
| **analyse** | PHPStan level 5 + Larastan | Static type analysis, unused code, type coverage |
| **test** | PHPUnit 11 | Full test suite (unit + feature) against a MariaDB service container |

See `.github/workflows/ci.yml` for the full pipeline definition.

## Testing

```bash
# Run full test suite (241 tests, 819 assertions)
docker exec api vendor/bin/phpunit --no-coverage

# Run unit tests only
docker exec api vendor/bin/phpunit --testsuite=Unit --no-coverage

# Run feature tests only
docker exec api vendor/bin/phpunit --testsuite=Feature --no-coverage

# Run from host (override DB_HOST for Docker-exposed port)
DB_HOST=127.0.0.1 php vendor/bin/phpunit --no-coverage

# Static analysis
docker exec api vendor/bin/phpstan analyse --memory-limit=512M

# Code style
docker exec api vendor/bin/phpcs
docker exec api vendor/bin/phpcbf   # auto-fix
```

### Test Coverage

| Area | Tests | What's covered |
|------|-------|---------------|
| **JSON:API Core** | SortField, ErrorObject, QueryParameters, Pagination, Document, AbstractTransformer | Parsing, serialization, sparse fieldsets, pagination math, links, error formatting |
| **Entities** | Recipe, Ingredient, Direction, DirectionNote, Operation, Procedure, Serving, Cuisine, Author | Constructors, defaults, setters/getters, soft deletes, computed fields |
| **Middleware** | ForceJsonResponse, ValidateJsonApi, CheckAuthorTier | Accept header forcing, content-type validation (415), tier enforcement (403) |
| **Exceptions** | NotFoundException, ValidationErrorException | JSON:API error rendering, field-level errors, MessageBag factory |
| **Recipe CRUD** | CrudTest (12 tests) | Index pagination/sorting, show by slug, 404, create minimal/full, validation, slug uniqueness |
| **Update/Delete** | UpdateDeleteTest (15 tests) | Update title/description/multi-field, publish sets published-at, idempotent PATCH, validation (invalid difficulty/status/negative prep-time), clear nullable fields, preserves unchanged fields, soft-delete, delete 404, full step-by-step workflow |
| **Cuisine Requests** | CuisineRequestTest (14 tests) | Create/list/show requests, approve creates cuisine + upgrades recipes, reject with notes, double-approve/reject rejection, recipe requires cuisine, non-existent cuisine link, recipe with cuisine-request |
| **Import/Export/Fork** | ImportExportTest (11 tests) | Export structure, round-trip import, fork deep-copy, fork independence, 404 handling |
| **Step-by-Step** | StepByStepTest (12 tests) | Add/remove ingredients, add/remove directions, auto-create ingredient, accumulate amounts, prep-time recalc, step injection |
| **Shopping Lists** | ShoppingListTest (7 tests) | CRUD lifecycle, add/list items |
| **Pantry** | PantryTest (7 tests) | CRUD lifecycle, consume, cookable recipes |
| **Plans** | PlansTest (4 tests) | List plans, show by slug, 404, subscription endpoint |
| **Ratings/Comments** | RatingsCommentsTest (5 tests) | List/create ratings, list/create comments, log activity |
| **Search** | SearchTest (17 tests) | Recipe full-text search, autocomplete, product search, pagination, filters, empty/no-match |
| **Auth & Contract** | AuthenticationTest, JsonApiContractTest | Login flows, JSON:API structure compliance, endpoint integration |

## JSON:API Compliance

Every response follows the JSON:API v1.1 specification:

- Top-level `jsonapi.version` member
- Resource objects with `type`, `id`, `attributes`, `relationships`, `links`
- Compound documents with `included` for sideloaded relationships
- Sparse fieldsets via `?fields[type]=attr1,attr2`
- Pagination via `?page[number]=1&page[size]=25` with `meta.page` and `links` (first, last, prev, next)
- Sorting via `?sort=-created-at,title` (prefix `-` for descending)
- Filtering via `?filter[key]=value`
- Error objects with `status`, `title`, `detail`, `source`, `meta`
- Content-Type `application/vnd.api+json` enforced on all requests and responses

## License

MIT
