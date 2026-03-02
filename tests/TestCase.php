<?php

namespace Tests;

use Doctrine\DBAL\Connection;
use Doctrine\ORM\Tools\SchemaTool;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;

abstract class TestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        $this->ensureSqliteTestDatabaseExists();

        parent::setUp();

        if (in_array(DatabaseTransactions::class, class_uses_recursive(static::class))) {
            $this->ensureTestDatabaseReady();
            $this->beginDoctrineTransaction();
        }
    }

    private function ensureSqliteTestDatabaseExists(): void
    {
        if (getenv('DB_CONNECTION') !== 'sqlite') {
            return;
        }

        $database = getenv('DB_DATABASE');
        if ($database === false || $database === '' || $database === ':memory:') {
            return;
        }

        $path = str_starts_with($database, '/') ? $database : (__DIR__ . '/../' . $database);
        if (!file_exists($path)) {
            $dir = dirname($path);
            if (!is_dir($dir)) {
                mkdir($dir, 0o755, true);
            }
            touch($path);
        }
    }

    private static bool $testDatabaseReady = false;

    private function ensureTestDatabaseReady(): void
    {
        $connection = config('database.default');
        $database = config('database.connections.' . $connection . '.database');
        $driver = config('database.connections.' . $connection . '.driver');

        if (self::$testDatabaseReady) {
            return;
        }

        if ($driver === 'sqlite' && $database !== ':memory:') {
            $path = str_starts_with($database, '/') ? $database : (__DIR__ . '/../' . $database);
            if (file_exists($path)) {
                unlink($path);
                touch($path);
            }
            $em = app('em');
            $schemaTool = new SchemaTool($em);
            $schemaTool->createSchema($em->getMetadataFactory()->getAllMetadata());
            $this->createMissingTablesForDoctrine($em->getConnection());
            $this->seedTestDataViaDoctrine();
        } else {
            Artisan::call('migrate', ['--force' => true]);
            $this->seedTestData();
        }

        app('em')->clear();
        self::$testDatabaseReady = true;
    }

    /**
     * Create tables that exist in Laravel migrations but not in Doctrine entities (SQLite).
     */
    private function createMissingTablesForDoctrine(Connection $conn): void
    {
        /** @lang SQLite */
        $conn->executeStatement(<<<'SQL'
            CREATE TABLE IF NOT EXISTS user_preferences (
                id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL,
                user_id INTEGER NOT NULL,
                spice_tolerance SMALLINT DEFAULT 50 NOT NULL,
                created_at DATETIME NOT NULL,
                updated_at DATETIME NOT NULL,
                UNIQUE (user_id),
                FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE
            )
            SQL);
        /** @lang SQLite */
        $conn->executeStatement(<<<'SQL'
            CREATE TABLE IF NOT EXISTS user_preferred_cuisines (
                id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL,
                user_id INTEGER NOT NULL,
                cuisine_id INTEGER NOT NULL,
                FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE,
                FOREIGN KEY (cuisine_id) REFERENCES cuisines (id) ON DELETE CASCADE,
                UNIQUE (user_id, cuisine_id)
            )
            SQL);
        /** @lang SQLite */
        $conn->executeStatement(<<<'SQL'
            CREATE TABLE IF NOT EXISTS user_excluded_products (
                id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL,
                user_id INTEGER NOT NULL,
                product_id INTEGER NOT NULL,
                FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE,
                FOREIGN KEY (product_id) REFERENCES products (id) ON DELETE CASCADE,
                UNIQUE (user_id, product_id)
            )
            SQL);
        /** @lang SQLite */
        $conn->executeStatement(<<<'SQL'
            CREATE TABLE IF NOT EXISTS user_dietary_restrictions (
                id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL,
                user_id INTEGER NOT NULL,
                allergen_id INTEGER NOT NULL,
                FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE,
                FOREIGN KEY (allergen_id) REFERENCES allergens (id) ON DELETE CASCADE,
                UNIQUE (user_id, allergen_id)
            )
            SQL);
    }

    /**
     * Seed minimal test data (MySQL/MariaDB via Laravel).
     */
    private function seedTestData(): void
    {
        $now = now();

        DB::table('plans')->insertOrIgnore([
            [
                'id' => 1, 'name' => 'Free', 'slug' => 'free', 'description' => 'Basic access',
                'price_monthly' => 0, 'price_yearly' => 0, 'currency' => 'USD',
                'sort_order' => 0, 'is_active' => true, 'created_at' => $now, 'updated_at' => $now,
            ],
            [
                'id' => 2, 'name' => 'Pro', 'slug' => 'pro', 'description' => 'Pro access',
                'price_monthly' => 9.99, 'price_yearly' => 99.99, 'currency' => 'USD',
                'sort_order' => 1, 'is_active' => true, 'created_at' => $now, 'updated_at' => $now,
            ],
            [
                'id' => 3, 'name' => 'Premium', 'slug' => 'premium', 'description' => 'Premium access',
                'price_monthly' => 19.99, 'price_yearly' => 199.99, 'currency' => 'USD',
                'sort_order' => 2, 'is_active' => true, 'created_at' => $now, 'updated_at' => $now,
            ],
        ]);
        DB::table('plan_features')->insertOrIgnore([
            [
                'id' => 1, 'plan_id' => 1, 'feature' => 'max_pantry_items', 'value' => '20',
                'created_at' => $now, 'updated_at' => $now,
            ],
            [
                'id' => 2, 'plan_id' => 2, 'feature' => 'max_pantry_items', 'value' => '200',
                'created_at' => $now, 'updated_at' => $now,
            ],
            [
                'id' => 3, 'plan_id' => 3, 'feature' => 'max_pantry_items', 'value' => 'unlimited',
                'created_at' => $now, 'updated_at' => $now,
            ],
        ]);
        DB::table('cuisines')->insertOrIgnore([
            [
                'id' => 1, 'name' => 'Italian', 'variant' => null, 'slug' => 'italian',
                'description' => 'Italian cuisine', 'created_at' => $now, 'updated_at' => $now,
            ],
            [
                'id' => 2, 'name' => 'Italian', 'variant' => 'Northern', 'slug' => 'italian-northern',
                'description' => 'Northern', 'created_at' => $now, 'updated_at' => $now,
            ],
            [
                'id' => 10, 'name' => 'Italian', 'variant' => 'Apulian', 'slug' => 'italian-apulian',
                'description' => 'Apulian', 'created_at' => $now, 'updated_at' => $now,
            ],
        ]);
        DB::table('measures')->insertOrIgnore([
            [
                'id' => 1, 'name' => 'gram', 'slug' => 'g', 'measure_type' => 'M',
                'base_id' => 1, 'factor' => 1.0, 'created_at' => $now, 'updated_at' => $now,
            ],
            [
                'id' => 2, 'name' => 'milliliter', 'slug' => 'ml', 'measure_type' => 'V',
                'base_id' => 2, 'factor' => 1.0, 'created_at' => $now, 'updated_at' => $now,
            ],
        ]);
        DB::table('dish_types')->insertOrIgnore([
            ['id' => 1, 'name' => 'dish', 'created_at' => $now, 'updated_at' => $now],
        ]);
        DB::table('products')->insertOrIgnore([
            [
                'id' => 1, 'name' => 'yeast', 'slug' => 'yeast-dry', 'description' => 'Yeast',
                'vegan' => true, 'vegetarian' => true, 'halal' => true, 'kosher' => true,
                'created_at' => $now, 'updated_at' => $now,
            ],
            [
                'id' => 2, 'name' => 'water', 'slug' => 'water', 'description' => 'Water',
                'vegan' => true, 'vegetarian' => true, 'halal' => true, 'kosher' => true,
                'created_at' => $now, 'updated_at' => $now,
            ],
            [
                'id' => 3, 'name' => 'salt', 'slug' => 'salt', 'description' => 'Table salt',
                'vegan' => true, 'vegetarian' => true, 'halal' => true, 'kosher' => true,
                'created_at' => $now, 'updated_at' => $now,
            ],
            [
                'id' => 4, 'name' => 'extra virgin olive oil', 'slug' => 'extra-virgin-olive-oil',
                'description' => 'Olive oil', 'vegan' => true, 'vegetarian' => true,
                'halal' => true, 'kosher' => true, 'created_at' => $now, 'updated_at' => $now,
            ],
            [
                'id' => 5, 'name' => 'mozzarella', 'slug' => 'mozzarella',
                'description' => 'Mozzarella cheese', 'vegan' => false, 'vegetarian' => true,
                'halal' => true, 'kosher' => true, 'created_at' => $now, 'updated_at' => $now,
            ],
        ]);
        $password = '$2y$04$EixZaYVK1psbw1izvbvTH.1FJQXSMYQFJGcBpQDKJVHfTj7K/xMHy';
        DB::table('users')->insertOrIgnore([
            [
                'id' => 1, 'username' => 'seed-user', 'name' => 'Seed User', 'email' => 'seed@test.example',
                'password' => $password, 'password_changed_at' => $now, 'created_at' => $now, 'updated_at' => $now,
            ],
        ]);
        DB::table('authors')->insertOrIgnore([
            [
                'id' => 1, 'user_id' => 1, 'name' => 'seed-user', 'email' => 'seed@test.example',
                'tier' => 'free', 'created_at' => $now, 'updated_at' => $now,
            ],
        ]);
        DB::table('recipes')->insertOrIgnore([
            [
                'id' => 1, 'slug' => 'pizza', 'title' => 'Pizza', 'description' => 'Classic pizza recipe',
                'status' => 'published', 'author_id' => 1, 'cuisine_id' => 1,
                'created_at' => $now, 'updated_at' => $now, 'published_at' => $now,
            ],
        ]);
        DB::table('servings')->insertOrIgnore([
            [
                'id' => 1, 'product_id' => 5, 'amount' => 200, 'measure_id' => 1,
                'created_at' => $now, 'updated_at' => $now,
            ],
        ]);
        DB::table('ingredients')->insertOrIgnore([
            ['id' => 1, 'recipe_id' => 1, 'serving_id' => 1, 'created_at' => $now, 'updated_at' => $now],
        ]);
    }

    /**
     * Seed minimal test data for SQLite via Doctrine (same connection as app under test).
     */
    private function seedTestDataViaDoctrine(): void
    {
        $conn = app('em')->getConnection();
        $now = date('Y-m-d H:i:s');

        /** @lang SQLite */
        $conn->executeStatement(
            "INSERT OR IGNORE INTO plans (id, name, slug, description, price_monthly, price_yearly, "
            . "currency, sort_order, is_active, created_at, updated_at) VALUES "
            . "(1, 'Free', 'free', 'Basic access', 0, 0, 'USD', 0, 1, ?, ?), "
            . "(2, 'Pro', 'pro', 'Pro access', 9.99, 99.99, 'USD', 1, 1, ?, ?), "
            . "(3, 'Premium', 'premium', 'Premium access', 19.99, 199.99, 'USD', 2, 1, ?, ?)",
            array_fill(0, 6, $now)
        );
        /** @lang SQLite */
        $conn->executeStatement(
            "INSERT OR IGNORE INTO plan_features (id, plan_id, feature, value, created_at, updated_at) "
            . "VALUES (1, 1, 'max_pantry_items', '20', ?, ?), (2, 2, 'max_pantry_items', '200', ?, ?), "
            . "(3, 3, 'max_pantry_items', 'unlimited', ?, ?)",
            array_fill(0, 6, $now)
        );
        /** @lang SQLite */
        $conn->executeStatement(
            "INSERT OR IGNORE INTO cuisines (id, name, variant, slug, description, created_at, updated_at) "
            . "VALUES (1, 'Italian', NULL, 'italian', 'Italian cuisine', ?, ?), "
            . "(2, 'Italian', 'Northern', 'italian-northern', 'Northern', ?, ?), "
            . "(10, 'Italian', 'Apulian', 'italian-apulian', 'Apulian', ?, ?)",
            array_fill(0, 6, $now)
        );
        /** @lang SQLite */
        $conn->executeStatement(
            "INSERT OR IGNORE INTO measures (id, name, slug, measure_type, base_id, factor, "
            . "created_at, updated_at) VALUES (1, 'gram', 'g', 'M', 1, 1.0, ?, ?), "
            . "(2, 'milliliter', 'ml', 'V', 2, 1.0, ?, ?)",
            array_fill(0, 4, $now)
        );
        /** @lang SQLite */
        $conn->executeStatement(
            "INSERT OR IGNORE INTO dish_types (id, name, created_at, updated_at) VALUES (1, 'dish', ?, ?)",
            [$now, $now]
        );
        /** @lang SQLite */
        $conn->executeStatement(
            "INSERT OR IGNORE INTO products (id, name, slug, description, vegan, vegetarian, halal, kosher, "
            . "created_at, updated_at) VALUES "
            . "(1, 'yeast', 'yeast-dry', 'Yeast', 1, 1, 1, 1, ?, ?), "
            . "(2, 'water', 'water', 'Water', 1, 1, 1, 1, ?, ?), "
            . "(3, 'salt', 'salt', 'Table salt', 1, 1, 1, 1, ?, ?), "
            . "(4, 'extra virgin olive oil', 'extra-virgin-olive-oil', 'Olive oil', 1, 1, 1, 1, ?, ?), "
            . "(5, 'mozzarella', 'mozzarella', 'Mozzarella cheese', 0, 1, 1, 1, ?, ?)",
            array_fill(0, 10, $now)
        );
        /** @lang SQLite */
        $conn->executeStatement(
            "INSERT OR IGNORE INTO users (id, username, name, email, password, password_changed_at, "
            . "created_at, updated_at) VALUES (1, 'seed-user', 'Seed User', 'seed@test.example', "
            . "'\$2y\$04\$EixZaYVK1psbw1izvbvTH.1FJQXSMYQFJGcBpQDKJVHfTj7K/xMHy', ?, ?, ?)",
            [$now, $now, $now]
        );
        /** @lang SQLite */
        $conn->executeStatement(
            "INSERT OR IGNORE INTO authors (id, user_id, name, email, tier, created_at, updated_at) "
            . "VALUES (1, 1, 'seed-user', 'seed@test.example', 'free', ?, ?)",
            [$now, $now]
        );
        /** @lang SQLite */
        $conn->executeStatement(
            "INSERT OR IGNORE INTO recipes (id, slug, title, description, status, author_id, cuisine_id, "
            . "created_at, updated_at, published_at) VALUES "
            . "(1, 'pizza', 'Pizza', 'Classic pizza recipe', 'published', 1, 1, ?, ?, ?)",
            [$now, $now, $now]
        );
        /** @lang SQLite */
        $conn->executeStatement(
            "INSERT OR IGNORE INTO servings (id, product_id, amount, measure_id, created_at, updated_at) "
            . "VALUES (1, 5, 200, 1, ?, ?)",
            [$now, $now]
        );
        /** @lang SQLite */
        $conn->executeStatement(
            "INSERT OR IGNORE INTO ingredients (id, recipe_id, serving_id, created_at, updated_at) "
            . "VALUES (1, 1, 1, ?, ?)",
            [$now, $now]
        );
    }

    protected function tearDown(): void
    {
        if (in_array(DatabaseTransactions::class, class_uses_recursive(static::class))) {
            $this->rollbackDoctrineTransaction();
        }

        parent::tearDown();
    }

    private function beginDoctrineTransaction(): void
    {
        $this->doctrineConnection()->beginTransaction();
    }

    private function rollbackDoctrineTransaction(): void
    {
        $conn = $this->doctrineConnection();

        while ($conn->isTransactionActive()) {
            $conn->rollBack();
        }

        app('em')->clear();
    }

    private function doctrineConnection(): Connection
    {
        return app('em')->getConnection();
    }
}
