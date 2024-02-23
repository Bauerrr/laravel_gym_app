<?php

use App\Http\Controllers\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use \App\Http\Controllers\ExerciseController;
use \App\Http\Controllers\DayController;
use \App\Http\Controllers\ScoreController;

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


Route::post('/signup', [AuthController::class, 'signup']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');

Route::apiResource('exercises', ExerciseController::class)->middleware('auth:sanctum');

Route::middleware('auth:sanctum')->group(function () {
   Route::get('/days/{name}', [DayController::class, 'show']);
   Route::post('/days/{name}', [DayController::class, 'addExercise']);
   Route::delete('/days/{name}', [DayController::class, 'deleteExercise']);
});

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/scores/{day_name}',[ScoreController::class, 'show']);
    Route::post('/scores/{day_name}', [ScoreController::class, 'store']);
});
