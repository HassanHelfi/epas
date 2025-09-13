<?php

use App\Http\Controllers\Api\V1\EmployeeController;
use App\Http\Controllers\Api\V1\PerformanceController;
use App\Models\User;
use Illuminate\Http\Request;
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



Route::middleware(['throttle:60,1'])->group(function () {
    Route::get('/employees', [EmployeeController::class, 'index'])
        ->name('employees.index');

    Route::get('/employees/{user}/performance', [PerformanceController::class, 'getUserPerformance'])
        ->name('employees.performance');
    Route::get('/departments/{department}/performance-summary', [PerformanceController::class, 'getDepartmentPerformanceSummary'])
        ->name('departments.performance-summary');
    Route::get('/performance/reports/{user}', [PerformanceController::class, 'getEmployeePerformanceReport'])
        ->name('performance.report');
    Route::get('/performance/departments/rankings', [PerformanceController::class, 'getDepartmentRankings'])
        ->name('performance.departments.rankings');

    Route::post('/performance/reviews', [PerformanceController::class, 'submitPeerReview'])
        ->middleware('throttle:10,1')
        ->name('performance.reviews.submit');
    Route::post('/performance/trigger-calculation', [PerformanceController::class, 'triggerPerformanceCalculation'])
        ->middleware('throttle:5,1') 
        ->name('performance.trigger-calculation');
});