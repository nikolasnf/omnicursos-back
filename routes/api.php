<?php

use App\Http\Controllers\Users\UserController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthWebController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\CourseController;
use App\Http\Controllers\Api\HomeController;
use App\Http\Controllers\Api\LeadController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\StripeController;
use App\Http\Controllers\Api\AccountController;
use App\Http\Controllers\Api\S3UploadController;
use App\Http\Controllers\Api\ProgressController;
use App\Http\Controllers\Api\FavoriteController;
use App\Http\Controllers\Api\VideoController;

require __DIR__.'/auth.php';

Route::middleware('auth:api')->group(function () {
    Route::get('/user', [UserController::class, 'show'])
        ->name('user.show');
});

Route::middleware(['auth:api', 'verified'])->group(function () {
    Route::patch('/user', [UserController::class, 'update'])
        ->name('user.update');

    Route::patch('/user/change-password', [UserController::class, 'changePassword'])
        ->name('user.change-password');

});

Route::post('/login', [AuthWebController::class, 'login']);
Route::middleware('auth')->post('/logout', [AuthWebController::class, 'logout']);


//Route::middleware('auth:api')->group(function () {
Route::get('/categorias-com-cursos', [CategoryController::class, 'index']);
//});


Route::get('/categorias/{slug}/cursos', [CourseController::class, 'getByCategory']);
Route::get('/home/cursos', [HomeController::class, 'featuredCourses']);
Route::post('/leads', [LeadController::class, 'store']);
Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);
Route::post('/reset-password', [AuthController::class, 'resetPassword']);
Route::post('/stripe/session', [StripeController::class, 'handleSession']);

Route::put('/courses/{id}', [CourseController::class, 'update']);
Route::post('/courses', [CourseController::class, 'store']);

Route::delete('/courses/{id}', [CourseController::class, 'destroy']);


Route::put('/courses/{id}/modules', [CourseController::class, 'updateCourseWithModules']);

Route::get('/courses/{id}', [CourseController::class, 'getModulesByCourse']);

Route::get('/courses-slug/{slug}', [CourseController::class, 'getBySlug']);

Route::get('/general-courses', [CourseController::class, 'indexWithCounts']);

Route::get('/account/plan', [AccountController::class, 'plan']);
Route::post('/account/stripe/sync', [AccountController::class, 'syncStripeData']);

Route::apiResource('categories', CategoryController::class);


Route::post('/upload', [S3UploadController::class, 'upload']);
Route::get('/courses/{id}/content', [CourseController::class, 'getCourseContent']);

Route::post('/progress/complete', [ProgressController::class, 'markCompleted']);
Route::get('/progress/{course_id}', [ProgressController::class, 'getProgress']);

Route::post('/favorites', [FavoriteController::class, 'toggleFavorite']);
Route::get('/favorites', [FavoriteController::class, 'listFavorites']);
Route::get('/favorites/{courseId}', [FavoriteController::class, 'isFavorite']);

Route::get('/media', [VideoController::class, 'proxyS3']);

