<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    /**
     * Migrate existing direction.ingredient_id into direction_ingredients,
     * then drop directions.ingredient_id.
     */
    public function up(): void
    {
        $rows = DB::table('directions')
            ->join('procedures', 'procedures.id', '=', 'directions.procedure_id')
            ->whereNotNull('directions.ingredient_id')
            ->whereNotNull('procedures.serving_id')
            ->select('directions.id as direction_id', 'directions.ingredient_id', 'procedures.serving_id')
            ->get();

        foreach ($rows as $row) {
            DB::table('direction_ingredients')->insert([
                'direction_id' => $row->direction_id,
                'ingredient_id' => $row->ingredient_id,
                'serving_id' => $row->serving_id,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        Schema::table('directions', function (Blueprint $table) {
            $table->dropForeign(['ingredient_id']);
            $table->dropColumn('ingredient_id');
        });
    }

    public function down(): void
    {
        Schema::table('directions', function (Blueprint $table) {
            $table->foreignId('ingredient_id')->nullable()->after('procedure_id')->constrained('ingredients', 'id');
        });

        $firstPerDirection = DB::table('direction_ingredients')
            ->orderBy('id')
            ->get()
            ->groupBy('direction_id');
        foreach ($firstPerDirection as $directionId => $rows) {
            $first = $rows->first();
            DB::table('directions')->where('id', $directionId)->update(['ingredient_id' => $first->ingredient_id]);
        }
    }
};
