<?php

use App\Http\Controllers\VideoController;
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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('video-upload', [VideoController::class, 'videoCompress']);

Route::post('video-hls', [VideoController::class, 'videoHLS']);

Route::post('video-hls-2', [VideoController::class, 'videoHLS2']);

Route::post('video-hls-demo', [VideoController::class, 'videoHLS_demo']);

