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
use App\Http\Controllers\LocationController;

// Rutas para los endpoints
Route::get('/regiones', [LocationController::class, 'getRegions']);
Route::get('/provincias/{idRegion}', [LocationController::class, 'getProvinces']);
Route::get('/distritos/{idProvince}', [LocationController::class, 'getDistricts']);
Route::get('/colegios/{ubigeo}', [LocationController::class, 'getSchools']);

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});