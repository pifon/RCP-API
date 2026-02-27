<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class () extends Migration {
    /**
     * Run the migration: remove all servings with amount = 0 from the DB.
     * Procedures that reference a 0-serving get serving_id set to NULL.
     * Ingredients that reference a 0-serving are deleted (0-amount ingredient is invalid).
     */
    public function up(): void
    {
        $zeroServingIds = DB::table('servings')
            ->where('amount', '<=', 0)
            ->pluck('id')
            ->all();

        if ($zeroServingIds === []) {
            return;
        }

        DB::table('procedures')
            ->whereIn('serving_id', $zeroServingIds)
            ->update(['serving_id' => null]);

        $ingredientIds = DB::table('ingredients')
            ->whereIn('serving_id', $zeroServingIds)
            ->pluck('id')
            ->all();

        if ($ingredientIds !== []) {
            DB::table('ingredient_notes')
                ->whereIn('ingredient_id', $ingredientIds)
                ->delete();
        }

        DB::table('ingredients')
            ->whereIn('serving_id', $zeroServingIds)
            ->delete();

        DB::table('servings')
            ->whereIn('id', $zeroServingIds)
            ->delete();
    }

    /**
     * Reverse: no-op (we cannot restore deleted 0-servings).
     */
    public function down(): void
    {
    }
};
