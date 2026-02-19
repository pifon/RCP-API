<?php

use Illuminate\Support\Facades\Route;

// ─── Me (current user) ───────────────────────────────────────
Route::prefix('me')->name('me.')->group(function () {
    Route::get('/', \App\Http\Controllers\v1\Me\Show::class)->name('show');
    Route::patch('/', \App\Http\Controllers\v1\Me\Update::class)->name('update');

    Route::get('/preferences', [\App\Http\Controllers\v1\Me\Preferences::class, 'show'])
        ->name('preferences.show');
    Route::patch('/preferences', [\App\Http\Controllers\v1\Me\Preferences::class, 'update'])
        ->name('preferences.update');

    Route::get('/following', \App\Http\Controllers\v1\Follow\Index::class)->name('following');

    Route::get('/subscription', [\App\Http\Controllers\v1\Me\Subscription::class, 'show'])
        ->name('subscription.show');
    Route::post('/subscription', [\App\Http\Controllers\v1\Me\Subscription::class, 'subscribe'])
        ->name('subscription.create');
    Route::delete('/subscription', [\App\Http\Controllers\v1\Me\Subscription::class, 'cancel'])
        ->name('subscription.cancel');
});

// ─── Plans (public catalog) ─────────────────────────────────
Route::prefix('plans')->name('plans.')->group(function () {
    Route::get('/', \App\Http\Controllers\v1\Plan\Index::class)->name('index');
    Route::get('/{slug}', \App\Http\Controllers\v1\Plan\Show::class)->name('show');
});

// ─── Recipes ─────────────────────────────────────────────────
Route::prefix('recipes')->name('recipes.')->group(function () {
    Route::get('/', \App\Http\Controllers\v1\Recipe\Index::class)->name('index');
    Route::post('/', \App\Http\Controllers\v1\Recipe\Create::class)->name('create');
    Route::post('/full', \App\Http\Controllers\v1\Recipe\CreateFull::class)->name('create-full');
    Route::post('/import', \App\Http\Controllers\v1\Recipe\Import::class)->name('import');
    Route::get('/{slug}', \App\Http\Controllers\v1\Recipe\Show::class)
        ->middleware('paid-recipe')
        ->name('show');
    Route::get('/{slug}/export', \App\Http\Controllers\v1\Recipe\Export::class)
        ->middleware('recipe-owner')
        ->name('export');
    Route::post('/{slug}/fork', \App\Http\Controllers\v1\Recipe\Fork::class)->name('fork');

    Route::get('/{slug}/preparation', \App\Http\Controllers\v1\Recipe\Preparation::class)
        ->middleware('paid-recipe')
        ->name('preparation');

    Route::get('/{slug}/ingredients', \App\Http\Controllers\v1\Recipe\IngredientIndex::class)->name('ingredients.index');
    Route::post('/{slug}/ingredients', \App\Http\Controllers\v1\Recipe\IngredientAdd::class)->name('ingredients.add');
    Route::delete('/{slug}/ingredients/{ingredientId}', \App\Http\Controllers\v1\Recipe\IngredientRemove::class)->name('ingredients.remove');

    Route::get('/{slug}/directions', \App\Http\Controllers\v1\Recipe\DirectionIndex::class)->name('directions.index');
    Route::post('/{slug}/directions', \App\Http\Controllers\v1\Recipe\DirectionAdd::class)->name('directions.add');
    Route::delete('/{slug}/directions/{directionId}', \App\Http\Controllers\v1\Recipe\DirectionRemove::class)->name('directions.remove');

    Route::get('/{slug}/ratings', \App\Http\Controllers\v1\Recipe\RatingIndex::class)->name('ratings.index');
    Route::post('/{slug}/ratings', \App\Http\Controllers\v1\Recipe\RatingCreate::class)->name('ratings.create');

    Route::get('/{slug}/comments', \App\Http\Controllers\v1\Recipe\CommentIndex::class)->name('comments.index');
    Route::post('/{slug}/comments', \App\Http\Controllers\v1\Recipe\CommentCreate::class)->name('comments.create');

    Route::post('/{slug}/activity', \App\Http\Controllers\v1\Recipe\LogActivity::class)->name('activity.log');
});

// ─── Collections ─────────────────────────────────────────────
Route::prefix('collections')->name('collections.')->group(function () {
    Route::get('/', \App\Http\Controllers\v1\Collection\Index::class)->name('index');
    Route::post('/', \App\Http\Controllers\v1\Collection\Create::class)
        ->middleware('feature:max_collections,collections')
        ->name('create');
    Route::get('/{id}', \App\Http\Controllers\v1\Collection\Show::class)->name('show');
    Route::patch('/{id}', \App\Http\Controllers\v1\Collection\Update::class)->name('update');
    Route::delete('/{id}', \App\Http\Controllers\v1\Collection\Destroy::class)->name('destroy');

    Route::post('/{collectionId}/items', \App\Http\Controllers\v1\Collection\AddItem::class)->name('items.add');
    Route::delete(
        '/{collectionId}/items/{itemId}',
        \App\Http\Controllers\v1\Collection\RemoveItem::class,
    )->name('items.remove');
});

// ─── Shopping Lists ──────────────────────────────────────────
Route::prefix('shopping-lists')->name('shopping-lists.')->group(function () {
    Route::get('/', \App\Http\Controllers\v1\ShoppingList\Index::class)->name('index');
    Route::post('/', \App\Http\Controllers\v1\ShoppingList\Create::class)
        ->middleware('feature:max_shopping_lists,shopping_lists')
        ->name('create');
    Route::get('/{id}', \App\Http\Controllers\v1\ShoppingList\Show::class)->name('show');
    Route::patch('/{id}', \App\Http\Controllers\v1\ShoppingList\Update::class)->name('update');
    Route::delete('/{id}', \App\Http\Controllers\v1\ShoppingList\Destroy::class)->name('destroy');

    Route::get('/{listId}/items', \App\Http\Controllers\v1\ShoppingList\Items::class)->name('items.index');
    Route::post('/{listId}/items', \App\Http\Controllers\v1\ShoppingList\AddItem::class)->name('items.add');
    Route::patch(
        '/{listId}/items/{itemId}',
        \App\Http\Controllers\v1\ShoppingList\CheckItem::class,
    )->name('items.check');
    Route::delete(
        '/{listId}/items/{itemId}',
        \App\Http\Controllers\v1\ShoppingList\RemoveItem::class,
    )->name('items.remove');
});

// ─── Pantry ──────────────────────────────────────────────────
Route::prefix('pantry')->name('pantry.')->group(function () {
    Route::get('/', \App\Http\Controllers\v1\Pantry\Index::class)->name('index');
    Route::post('/', \App\Http\Controllers\v1\Pantry\Create::class)
        ->middleware('feature:max_pantry_items,pantry_items')
        ->name('create');
    Route::get('/cookable', \App\Http\Controllers\v1\Pantry\CookableRecipes::class)->name('cookable');
    Route::get('/{id}', \App\Http\Controllers\v1\Pantry\Show::class)->name('show');
    Route::patch('/{id}', \App\Http\Controllers\v1\Pantry\Update::class)->name('update');
    Route::post('/{id}/consume', \App\Http\Controllers\v1\Pantry\Consume::class)->name('consume');
    Route::delete('/{id}', \App\Http\Controllers\v1\Pantry\Destroy::class)->name('destroy');
});

// ─── Follows ─────────────────────────────────────────────────
Route::prefix('follows')->name('follows.')->group(function () {
    Route::post('/', \App\Http\Controllers\v1\Follow\Create::class)->name('create');
    Route::delete('/{id}', \App\Http\Controllers\v1\Follow\Destroy::class)->name('destroy');
});

// ─── Cuisines ────────────────────────────────────────────────
Route::prefix('cuisines')->name('cuisines.')->group(function () {
    Route::get('/', \App\Http\Controllers\v1\Cuisine\Catalog::class)->name('index');
    Route::get('/{slug}', \App\Http\Controllers\v1\Cuisine\Show::class)->name('show');
});

// ─── Authors ─────────────────────────────────────────────────
Route::prefix('authors')->name('authors.')->group(function () {
    Route::get('/{slug}', \App\Http\Controllers\v1\Author\Show::class)->name('show');
});

// ─── Dish Types ──────────────────────────────────────────────
Route::prefix('dish-types')->name('dish-types.')->group(function () {
    Route::get('/{slug}', \App\Http\Controllers\v1\Dishtype\Show::class)->name('show');
});
