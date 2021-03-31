<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\V1\Blog\CategoryController;
use App\Http\Controllers\API\V1\Blog\BlogController;
use App\Http\Controllers\API\V1\ResearchController;
use App\Http\Controllers\API\V1\ProjectController;
use App\Http\Controllers\API\V1\User\AuthController;
use App\Http\Controllers\API\V1\User\VerificationController;
use App\Http\Controllers\API\V1\User\UserController;


// Route::middleware('auth:api')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::group(['prefix' => 'v1', 'namespace' => 'API\V1'], function() {

	Route::group(['namespace' => 'User'], function() {
		Route::get('email/verify/{id}', [VerificationController::class, 'verify'])->name('verification.verify');
		Route::get('email/resend', [VerificationController::class, 'resend'])->name('verification.resend');
		Route::post('register', [AuthController::class, 'register']);
		Route::post('login', [AuthController::class, 'login']);
		Route::get('user/{id}', [UserController::class, 'get']);
	});  

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

	Route::group(['prefix' => 'research'], function() {
		Route::get('view/{code}', [ResearchController::class, 'showSingle']);
		Route::get('/', [ResearchController::class, 'index']);
		Route::get('show/{id}', [ResearchController::class, 'showSingle']);
		Route::post('/', [ResearchController::class, 'store']);
		Route::delete('delete/{id}', [ResearchController::class, 'delete']);
	});

	Route::group(['prefix' => 'project'], function() {
		Route::get('view/{code}', [ProjectController::class, 'showSingle']);
		Route::get('/', [ProjectController::class, 'index']);
		Route::get('show/{id}', [ProjectController::class, 'showSingle']);
		Route::post('/', [ProjectController::class, 'store']);
		Route::post('update/{id}', [ProjectController::class, 'update']);
		Route::delete('delete/{id}', [ProjectController::class, 'delete']);
	});
});
