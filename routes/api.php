<?php


use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CreateStoreControllerAction;
use App\Http\Controllers\GetNearbyStoreControllerAction;
use App\Http\Controllers\GetStoreByDeliveryRangeAction;

Route::post('/stores', CreateStoreControllerAction::class);
Route::get('/stores', GetNearbyStoreControllerAction::class);
