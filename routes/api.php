<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\V1\Blog\CategoryController;
use App\Http\Controllers\API\V1\Blog\BlogController;

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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::group(['prefix' => 'v1', 'namespace' => 'API\V1'], function() {
	Route::group(['namespace' => 'Blog'], function(){
		Route::get('blog/view/{slug}', [BlogController::class, 'showSingle']);
		Route::get('blog/category/{slug}', [CategoryController::class, 'showSingle']);

		Route::get('blog', [BlogController::class, 'index']);
		Route::get('blog/show/{id}', [BlogController::class, 'showSingle']);
		Route::post('blog', [BlogController::class, 'store']);
		Route::post('blog/update/{id}', [BlogController::class, 'update']);
		Route::delete('blog/delete/{id}', [BlogController::class, 'delete']);

		Route::get('category/show/{id}', [CategoryController::class, 'showSingle']);
		Route::get('category', [CategoryController::class, 'index']);
		Route::post('category', [CategoryController::class, 'store']);
		Route::post('category/update/{id}', [CategoryController::class, 'update']);
		Route::delete('category/delete/{id}', [CategoryController::class, 'delete']);
	});
});
