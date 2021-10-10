<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\PostController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
Route::prefix('v1')->group(function () {
    Route::get('posts', [PostController::class, 'Index'])->name("PostIndex");
    Route::post('posts', [PostController::class, 'Create'])->name("PostCreate");
    Route::post('posts/{id}', [PostController::class, 'Comment'])->name("Comment");
    Route::patch('posts/{id}', [PostController::class, 'Edit'])->name("PostEdit");
    Route::get('posts/{id}', [PostController::class, 'View'])->name("PostView");
    Route::delete('posts/{id}', [PostController::class, 'Delete'])->name("PostDelete");
});
