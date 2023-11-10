<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthenticationController;
use App\Http\Controllers\Api\ArticleController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });


Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    Route::post('/article/preference/{id}', [ArticleController::class, 'updatePreferences'])->name('articles.preference');
});

Route::get('/articles', [ArticleController::class, 'articlesListing'])->name('articles.listing');
Route::get('/article/{id}', [ArticleController::class, 'findArticle'])->name('articles.find');
Route::get('/article/preference', [ArticleController::class, 'prefs'])->name('articles.preference.prefs');

Route::post('/registration', [AuthenticationController::class, 'registration'])->name('user.registration');
Route::post('/login', [AuthenticationController::class, 'login'])->name('user.login');