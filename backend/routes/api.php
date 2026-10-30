<?php

use App\Http\Controllers\Api\ProblemController;
use App\Http\Controllers\Api\TopicController;
use App\Http\Controllers\HealthController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\LeetCodeController;
use App\Http\Controllers\Api\AnalyticsController;

Route::get('/health', [HealthController::class, 'check']);

Route::get('/problems', [ProblemController::class, 'index']);
Route::get('/topics', [TopicController::class, 'index']);


Route::get('/users/{username}/skill-stats', [LeetCodeController::class,'skillStats',]);
Route::get('/users/{username}/submissions', [LeetCodeController::class,'submissions',]);
Route::get('/users/{username}/analytics', [AnalyticsController::class, 'show']);
