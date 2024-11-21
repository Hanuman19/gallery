<?php

use App\Http\Controllers\Api\GalleryController;
use Illuminate\Support\Facades\Route;

Route::post('/upload', [GalleryController::class, 'upload']);
Route::get('/get-pictures', [GalleryController::class, 'index']);
Route::get('/get-picture/{id}', [GalleryController::class, 'show']);
