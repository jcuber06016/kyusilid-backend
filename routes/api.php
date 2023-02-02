<?php

use App\Http\Controllers\AnnouncementController;
use App\Http\Controllers\ClasslistController;
use App\Http\Controllers\Classworkcontroller;
use App\Http\Controllers\PersonlistController;
use App\Http\Controllers\UserController;
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
Route::post('add-announcement',[AnnouncementController::class, 'addannouncement']);
Route::get('get-announcement/{id?}',[AnnouncementController::class, 'getannouncement']);
Route::get('get-announcementforstudent/{id?}',[AnnouncementController::class, 'getannouncementforstudent']);
Route::get('get-topiclist/{id?}',[Classworkcontroller::class, 'gettopics']);
Route::get('get-activitylist/{id?}',[Classworkcontroller::class, 'getactivities']);
Route::get('getcommentcount_act/{id?}',[Classworkcontroller::class, 'getcommentcount']);

//Route for user login
Route::post('login',[UserController::class, 'login']);
Route::post('register',[UserController::class, 'register']);




Route::get('classlist',[ClasslistController::class, 'index']);


Route::get('getclasslist/{id?}' ,[ClasslistController::class ,'getclasslist']);
Route::get('getclasslist_archived/{id?}' ,[ClasslistController::class ,'getclasslist_archived']);
Route::get('getusertype/{id?}' ,[ClasslistController::class ,'checkusertype']);
Route::get('getpersonlist/{id?}' , [PersonlistController::class, 'getpersonlist']);
Route::get('getcomments/{id?}' , [AnnouncementController::class, 'getcomments']);








Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
