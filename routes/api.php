<?php


use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CreateStoreControllerAction;
use App\Http\Controllers\FindStoreByPostcodeControllerAction;

Route::post('/stores', CreateStoreControllerAction::class);
Route::get('/stores', FindStoreByPostcodeControllerAction::class);
