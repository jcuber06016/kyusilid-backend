<?php

use App\Http\Controllers\AnnouncementController;

use App\Http\Controllers\ClasslistController;
use App\Http\Controllers\Classworkcontroller;
use App\Http\Controllers\PersonlistController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\MessagesController;
use App\Http\Controllers\CrudStudController;
use App\Http\Controllers\TimeController;
use App\Http\Controllers\ImportClsController;
use App\Http\Controllers\ImportController;
use App\Http\Controllers\ImportProfController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Response;
use App\Http\Controllers\QuizController;
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


//Route for getfile

Route::get('/getfile/{filepath}', function ($filepath) {
    $path = storage_path('app/public/laravel/public' . $filepath);
    if (!File::exists($path)) {
        abort(404);
    }
    $file = File::get($path);
    $type = File::mimeType($path);
    $response = Response::make($file, 200);
    $response->header("Content-Type", $type);
    return $response;
})->name('getfile');

//Route for profile pic change
Route::post('profile-pic/{id?}', [UserController::class, 'updateProfilePic']);


//Route for Import
Route::post('import-excel', [ImportController::class, 'import']);
Route::post('import-class', [ImportClsController::class, 'importclass']);
Route::post('import-prof', [ImportProfController::class, 'importprof']);

//Route for announcement
Route::get('current-time', [TimeController::class, 'getCurrentTime']);
Route::post('add-announcement',[AnnouncementController::class, 'addannouncement']);
Route::get('get-announcement/{id?}',[AnnouncementController::class, 'getannouncement']);
Route::get('get-announcementforstudent/{id?}',[AnnouncementController::class, 'getannouncementforstudent']);
Route::get('get-topiclist/{id?}',[Classworkcontroller::class, 'gettopics']);
Route::get('get-activitylist/{id?}',[Classworkcontroller::class, 'getactivities']);
Route::get('getcommentcount_act/{id?}',[Classworkcontroller::class, 'getcommentcount']);
Route::put('updateannouncement', [AnnouncementController::class, 'announcementpostnow']); // update the scheduled time for an announcement by ID


//Route for user login
Route::post('login',[UserController::class, 'login']);
Route::post('changepass',[UserController::class, 'changepass']);
Route::post('register',[UserController::class, 'register']);
Route::post('reset-pass',[UserController::class, 'resetPassword']);


Route::post('createtopic',[Classworkcontroller::class , 'createtopic']);
Route::get('getprofilestatus/{id?}',[Classworkcontroller::class , 'getprofilestatus']);
Route::post('createactivity/{id?}',[Classworkcontroller::class, 'createactivity']);

Route::post('postcomment',[AnnouncementController::class, 'postcomment']);

Route::get('accountlist/{id?}' , [DepartmentController::class, 'accountlist']);
Route::get('getadminlog' , [DepartmentController::class, 'getadminlog']);


Route::get('classlist',[ClasslistController::class, 'index']);


Route::get('getclasslist/{id?}' ,[ClasslistController::class ,'getclasslist']);
Route::get('getclasslist_archived/{id?}' ,[ClasslistController::class ,'getclasslist_archived']);
Route::get('getusertype/{id?}' ,[ClasslistController::class ,'checkusertype']);
Route::get('getpersonlist/{id?}' , [PersonlistController::class, 'getpersonlist']);

Route::get('getgradelist/{id?}' , [PersonlistController::class, 'getgradelist']);

Route::get('getcomments/{id?}' , [AnnouncementController::class, 'getcomments']);
Route::get('getstudentlist/{id?}' , [PersonlistController::class, 'getstudentlist']);
Route::get('getactivitycommentlist/{id?}' , [Classworkcontroller::class, 'getactivitycommentlist']);
Route::get('getdeptinfo/{id?}' , [PersonlistController::class, 'getdeptinfo']);
Route::get('getdepartment/{id?}' , [DepartmentController::class, 'getdepartment']);
Route::get('getclass_log/{id?}' , [Classworkcontroller::class, 'getclass_log']);
Route::get('getallclasses/{id?}' , [Classworkcontroller::class, 'getallclasses']);
Route::get('getallsubjects/{id?}' , [Classlistcontroller::class, 'getallsubjects']);
Route::get('getcreateclassvalue/{id?}' , [ClasslistController::class, 'getcreateclassvalue']);

Route::get('getadminannouncement/{id?}' , [AnnouncementController::class, 'getadminannouncement']);


Route::put('createadminannouncement' , [AnnouncementController::class, 'createadminannouncement']);


Route::get('getmessages/{id?}', [MessagesController::class, 'getmessages']);
Route::put('createmessage' , [MessagesController::class, 'createmessage']);


//CRUD 
//Student
Route::post('addstud',[CrudStudController::class,'addstud']);
Route::get('studentbl',[CrudStudController::class], 'studentbl');
Route::post('importstud',[CrudStudController::class,'importstud']);
//Professor
Route::post('addprof',[CrudStudController::class,'addprof']);
//Subject
Route::post('addsubj',[CrudStudController::class,'addsubj']);
Route::get('getactivitystatus/{id?}/{id2?}' , [Classworkcontroller::class, 'getactivitystatus']);

//postactivity 
Route::put('postactivity' , [Classworkcontroller::class, 'postactivity'] );
Route::post('uploadfile' ,[Classworkcontroller::class, 'uploadfile']);
Route::post('updateactivity' ,[Classworkcontroller::class, 'updateactivity']);
Route::post('uploadstudent' ,[Classworkcontroller::class, 'uploadstudent']);



Route::post('setuseractivate' , [UserController::class , 'setuseractivate']);


Route::get('announcementpostnow/{id?}' , [AnnouncementController::class, 'announcementpostnow']);
Route::put('createactivitycomment' , [Classworkcontroller::class, 'createactivitycomment']);
Route::post('attendance-in' , [AttendanceController::class, 'attendancein']);
Route::delete('deletadminannouncement/{id?}' , [AnnouncementController::class, 'deletadminannouncement']);
Route::delete('deletemessage/{id?}' , [MessagesController::class, 'deletemessage']);
Route::delete('deleteannouncement/{id?}', [AnnouncementController::class, 'deleteannouncement']);
Route::put('createclass', [Classlistcontroller::class, 'createclass']);
Route::delete('deleteannouncementcomment/{id?}', [AnnouncementController::class, 'deleteannouncementcomment'] );
Route::delete('deleteactivity/{id?}' , [Classworkcontroller::class ,'deleteactivity']);
Route::delete('deleteactivitycomment/{id?}' , [Classworkcontroller::class ,'deleteactivitycomment']);

Route::get('gettopicstring/{id?}' , [Classworkcontroller::class, 'gettopicstring']);
// gettopicstring

Route::get('gettopicsbysubject/{id?}',[Classworkcontroller::class, 'gettopicsbysubject']);

Route::get('getactivityresponses/{id?}',[Classworkcontroller::class, 'getactivityresponses']);


Route::post('posttomodule' , [Classworkcontroller::class, 'posttomodule']);


Route::post('admincreatemodule2' , [Classworkcontroller::class, 'admincreatemodule2']);
Route::get('admindashboard/{id?}' , [ClasslistController::class, 'admindashboard']);
Route::post('updateclassinfosettings' , [ClasslistController::class, 'updateclassinfosettings']);
Route::post('handIn' , [Classworkcontroller::class, 'handIn']);
Route::post('unSubmit' , [Classworkcontroller::class, 'unSubmit']);
Route::post('setGrade' , [Classworkcontroller::class, 'setGrade']);



Route::get('getclasslist2/{id?}' , [DepartmentController::class, 'getclasslist2']);
Route::get('getsubjectlist/{id?}' , [DepartmentController::class, 'getsubjectlist']);

Route::post('updateclasslist' ,[Classlistcontroller::class , 'updateclasslist']);

Route::put('adminlog' , [DepartmentController::class, 'adminlog']);



Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('quiz/{id}', [QuizController::class, 'getQuiz']);
Route::get('quiz/{id}/questions', [QuizController::class, 'getQuizQuestions']);
Route::get('quiz-title/{id}', [QuizController::class, 'getQuizTitle']);
Route::get('quiz-title', [QuizController::class, 'getQuizTitleAll']);
Route::post('quiz', [QuizController::class, 'addQuiz']);
Route::post('quiz-question', [QuizController::class, 'addQuizQuestion']);
Route::put('quiz/{id}', [QuizController::class, 'updateQuiz']);
Route::put('quiz-question/{id}', [QuizController::class, 'updateQuizQuestion']);
Route::delete('quiz/{id}', [QuizController::class, 'deleteQuiz']);
Route::delete('quiz-question/{id}', [QuizController::class, 'deleteQuizQuestion']);

