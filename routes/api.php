<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Blog\PostController;
use App\Http\Controllers\Api\Blog\Admin\CategoryController; // ← додати

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::group(['prefix' => 'blog'], function () {
    Route::apiResource('posts', PostController::class)->names('blog.posts');
});

// Адмінка
Route::group(['prefix' => 'blog/admin'], function () { // ← виправити prefix
    $methods = ['index', 'store', 'update'];
    Route::apiResource('categories', CategoryController::class)
        ->only($methods)
        ->names('blog.admin.categories');
});
//BlogPost
Route::apiResource('posts', PostController::class)
    ->except(['show'])                               //не робити маршрут для метода show
    ->names('blog.admin.posts');

Route::group(['prefix' => 'digging_deeper'], function () {
    Route::get('process-video', 'DiggingDeeperController@processVideo')
        ->name('digging_deeper.processVideo');

    Route::get('prepare-catalog', 'DiggingDeeperController@prepareCatalog')
        ->name('digging_deeper.prepareCatalog');
});
