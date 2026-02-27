<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    /**
     * Drop the Marinade product and all references to it (servings, ingredients, direction_ingredients, etc.).
     */
    public function up(): void
    {
        $product = DB::table('products')
            ->where('slug', 'marinade')
            ->orWhere('name', 'Marinade')
            ->first();

        if ($product === null) {
            return;
        }

        $marinadeId = $product->id;
        $servingIds = DB::table('servings')->where('product_id', $marinadeId)->pluck('id')->all();

        if ($servingIds !== []) {
            $ingredientIds = DB::table('ingredients')->whereIn('serving_id', $servingIds)->pluck('id')->all();

            if ($ingredientIds !== []) {
                DB::table('direction_ingredients')->whereIn('ingredient_id', $ingredientIds)->delete();
                DB::table('ingredient_notes')->whereIn('ingredient_id', $ingredientIds)->delete();
            }

            DB::table('procedures')->whereIn('serving_id', $servingIds)->update(['serving_id' => null]);
            DB::table('ingredients')->whereIn('serving_id', $servingIds)->delete();
            DB::table('servings')->where('product_id', $marinadeId)->delete();
        }

        if (Schema::hasTable('product_group_members')) {
            DB::table('product_group_members')->where('product_id', $marinadeId)->delete();
        }
        if (Schema::hasTable('product_substitutions')) {
            DB::table('product_substitutions')
                ->where('product_id', $marinadeId)
                ->orWhere('substitute_id', $marinadeId)
                ->delete();
        }
        if (Schema::hasTable('pantry_items')) {
            DB::table('pantry_items')->where('product_id', $marinadeId)->delete();
        }
        if (Schema::hasTable('shopping_list_items')) {
            DB::table('shopping_list_items')->where('product_id', $marinadeId)->delete();
        }
        if (Schema::hasTable('user_excluded_products')) {
            DB::table('user_excluded_products')->where('product_id', $marinadeId)->delete();
        }
        if (Schema::hasTable('product_allergens')) {
            DB::table('product_allergens')->where('product_id', $marinadeId)->delete();
        }
        if (Schema::hasTable('product_sensations')) {
            DB::table('product_sensations')->where('product_id', $marinadeId)->delete();
        }
        if (Schema::hasTable('pantry_logs')) {
            DB::table('pantry_logs')->where('product_id', $marinadeId)->delete();
        }

        DB::table('products')->where('id', $marinadeId)->delete();
    }

    public function down(): void
    {
        // Cannot restore deleted product and its references.
    }
};
