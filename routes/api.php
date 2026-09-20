<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\SubjectController;
use App\Http\Controllers\Api\LiveClassController;
use App\Http\Controllers\Api\TutorController;
use App\Http\Controllers\Api\EvaluationController;
use App\Http\Controllers\Api\BillingController;

Route::post('/auth/register', [AuthController::class, 'register']);
Route::post('/auth/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/auth/logout', [AuthController::class, 'logout']);
    Route::get('/user', function (Request $request) {
        return clone $request->user()->load('plan'); // Devuelve usuario con su plan
    });

    // Subjects
    Route::get('/subjects', [SubjectController::class, 'index']);
    Route::get('/subjects/{id}', [SubjectController::class, 'show']);

    // Live Classes
    Route::get('/live-classes', [LiveClassController::class, 'index']);
    Route::get('/live-classes/{id}', [LiveClassController::class, 'show']);

    // Tutor IA (Nexa)
    Route::post('/tutor/session', [TutorController::class, 'startSession']);
    Route::post('/tutor/session/{id}/message', [TutorController::class, 'sendMessage']);
    Route::get('/tutor/session/{id}/history', [TutorController::class, 'getHistory']);

    // Evaluations
    Route::get('/evaluations', [EvaluationController::class, 'index']);
    Route::post('/evaluations', [EvaluationController::class, 'store']);
    Route::get('/tasks', [App\Http\Controllers\Api\TaskController::class, 'index']);

    // Billing / Plans
    Route::get('/plans', [BillingController::class, 'getPlans']);
    Route::get('/my-plan', [BillingController::class, 'myPlan']);

    // Admin Routes
    Route::middleware(function ($request, $next) {
        if ($request->user()->role !== 'admin') abort(403, 'Unauthorized');
        return $next($request);
    })->prefix('admin')->group(function () {
        Route::get('/subjects', [\App\Http\Controllers\Api\AdminController::class, 'getSubjects']);
        Route::post('/subjects', [\App\Http\Controllers\Api\AdminController::class, 'storeSubject']);
        Route::put('/subjects/{id}', [\App\Http\Controllers\Api\AdminController::class, 'updateSubject']);
        Route::delete('/subjects/{id}', [\App\Http\Controllers\Api\AdminController::class, 'destroySubject']);

        Route::get('/live-classes', [\App\Http\Controllers\Api\AdminController::class, 'getLiveClasses']);
        Route::post('/live-classes', [\App\Http\Controllers\Api\AdminController::class, 'storeLiveClass']);
        Route::put('/live-classes/{id}', [\App\Http\Controllers\Api\AdminController::class, 'updateLiveClass']);
        Route::delete('/live-classes/{id}', [\App\Http\Controllers\Api\AdminController::class, 'destroyLiveClass']);

        Route::get('/prompt', [\App\Http\Controllers\Api\AdminController::class, 'getPrompt']);
        Route::put('/prompt', [\App\Http\Controllers\Api\AdminController::class, 'updatePrompt']);
    });
});
