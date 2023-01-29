<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\DB;

use Illuminate\Http\Request;

class AnnouncementController extends Controller
{
    public function getannouncement($id = null){
        return DB::table('announcement')
        ->join('classinfo' , 'classinfo.classes_id' , "=", 'announcement.classes_id')
        ->join('login' , 'login.acc_id' , "=", 'announcement.acc_id')
        ->select('an_title' ,'an_content', 'created_at', 'updated_at', 'status', 'login.lastname' , 'login.firstname' , 'login.middle' , 'login.suffix' , 'login.title' , 'login.acc_id')
        ->where('classinfo.classes_id', $id)
        ->get();
    }

    public function addannouncement($announcement){
        
    }

    //
}
