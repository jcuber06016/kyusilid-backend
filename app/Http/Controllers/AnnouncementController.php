<?php

namespace App\Http\Controllers;

use App\Models\announcementcomments;
use Illuminate\Support\Facades\DB;

use Illuminate\Http\Request;

class AnnouncementController extends Controller
{
    public function getannouncement($id = null){
        return DB::table('announcement')
        ->join('classinfo' , 'classinfo.classes_id' , "=", 'announcement.classes_id')
        ->join('login' , 'login.acc_id' , "=", 'announcement.acc_id')
        ->select('an_title' ,'an_content', 'created_at', 'updated_at', 'status', 'login.lastname' , 'login.firstname' , 'login.middle' , 'login.suffix' , 'login.title' , 'login.acc_id' ,'announcement.an_id')
        ->where('classinfo.classes_id', $id)
        ->get();
    }

    public function addannouncement($announcement){
        
    }


    public function getcomments($id){
        return DB:: table('announcementcomments')
        ->join('login' , "login.acc_id" , "=", "announcementcomments.acc_id")
        ->select('login.firstname', 'login.middle' , 'login.lastname', 'login.title', 'login.suffix' ,'announcementcomments.date_posted' , 'announcementcomments.com_content')
        ->where('announcementcomments.an_id' , $id)
        ->get();
    }

    //
}
