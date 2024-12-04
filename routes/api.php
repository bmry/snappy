<?php


use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CreateStoreControllerAction;
use App\Http\Controllers\GetNearbyStoreControllerAction;

Route::post('/stores', CreateStoreControllerAction::class);
Route::get('/stores', GetNearbyStoreControllerAction::class);
