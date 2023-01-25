<?php

use App\Http\Controllers\AnnouncementController;
use App\Http\Controllers\ClasslistController;
use App\Http\Controllers\UserController;
use App\Models\ClassListModel;
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
//Route for announcement
Route::post('add-announcement',[AnnouncementController::class, 'store']);
Route::get('get-announcement',[AnnouncementController::class, 'index']);


//Route for user login
Route::post('login',[UserController::class, 'login']);
Route::post('register',[UserController::class, 'register']);


Route::get('classlist',[ClasslistController::class, 'index']);


Route::get('getclasslist/{id?}' ,[ClasslistController::class ,'getclasslist']);







Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
