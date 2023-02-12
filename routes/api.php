<?php

use App\Http\Controllers\AnnouncementController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\ClasslistController;
use App\Http\Controllers\Classworkcontroller;
use App\Http\Controllers\DepartmentController;
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
Route::put('updateannouncement', [AnnouncementController::class, 'announcementpostnow']); // update the scheduled time for an announcement by ID


//Route for user login
Route::post('login',[UserController::class, 'login']);
Route::post('register',[UserController::class, 'register']);
Route::post('createtopic',[Classworkcontroller::class , 'createtopic']);
Route::post('createactivity/{id?}',[Classworkcontroller::class, 'createactivity']);
Route::post('postcomment',[AnnouncementController::class, 'postcomment']);



Route::get('classlist',[ClasslistController::class, 'index']);


Route::get('getclasslist/{id?}' ,[ClasslistController::class ,'getclasslist']);
Route::get('getclasslist_archived/{id?}' ,[ClasslistController::class ,'getclasslist_archived']);
Route::get('getusertype/{id?}' ,[ClasslistController::class ,'checkusertype']);
Route::get('getpersonlist/{id?}' , [PersonlistController::class, 'getpersonlist']);
Route::get('getcomments/{id?}' , [AnnouncementController::class, 'getcomments']);
Route::get('getstudentlist/{id?}' , [PersonlistController::class, 'getstudentlist']);
Route::get('getactivitycommentlist/{id?}' , [Classworkcontroller::class, 'getactivitycommentlist']);

Route::get('getdeptinfo/{id?}' , [PersonlistController::class, 'getdeptinfo']);

Route::get('getdepartment/{id?}' , [DepartmentController::class, 'getdepartment']);



Route::get('announcementpostnow/{id?}' , [AnnouncementController::class, 'announcementpostnow']);

Route::put('createactivitycomment' , [Classworkcontroller::class, 'createactivitycomment']);


Route::post('attendance-in' , [AttendanceController::class, 'attendancein']);




Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
