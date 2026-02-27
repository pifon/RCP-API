<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class () extends Migration {
    private const CONSTRAINT_NAME = 'servings_amount_positive';

    /**
     * Run the migration: enforce that servings.amount is strictly greater than 0.
     * MySQL 8.0.16+ / MariaDB 10.2.1+ support CHECK constraints.
     */
    public function up(): void
    {
        $driver = DB::getDriverName();
        if (in_array($driver, ['mysql', 'mariadb', 'pgsql'], true)) {
            DB::statement('ALTER TABLE servings ADD CONSTRAINT ' . self::CONSTRAINT_NAME . ' CHECK (amount > 0)');
        }
    }

    /**
     * Reverse the migration.
     */
    public function down(): void
    {
        $driver = DB::getDriverName();
        if (in_array($driver, ['mysql', 'mariadb', 'pgsql'], true)) {
            DB::statement('ALTER TABLE servings DROP CONSTRAINT ' . self::CONSTRAINT_NAME);
        }
    }
};
