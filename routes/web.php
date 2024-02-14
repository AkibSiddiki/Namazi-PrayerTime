<?php

use Illuminate\Support\Facades\Route;


Route::get('/', [\App\Http\Controllers\NamazController::class, 'permissionView'])->name('permission.view');
Route::post('/current-location-prayer-times', [\App\Http\Controllers\NamazController::class, 'geoLocationPrayerTimes'])->name('getPrayerTimes');
Route::get('/{districtName}', [\App\Http\Controllers\NamazController::class, 'districtPrayerTimes']);
