<?php

use App\Http\Controllers\API\V1\MovieController;
use Illuminate\Support\Facades\Route;

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

Route::prefix('v1')->group(function () {
    Route::get('longest-duration-movies', [MovieController::class, 'longestDurationMovies']);
    Route::post('new-movie', [MovieController::class, 'save']);
    Route::get('top-rated-movies', [MovieController::class, 'topRatedMovies']);
});
