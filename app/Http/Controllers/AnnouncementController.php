<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\DB;

use Illuminate\Http\Request;

class AnnouncementController extends Controller
{
    public function getannouncement($id = null){
        return DB::table('announcement')
        ->join('classinfo' , 'classinfo.classes_id' , "=", 'announcement.classes_id')
        ->join('professor' , 'professor.acc_id' , "=", 'announcement.acc_id')
        ->select('an_title' ,'an_content', 'created_at', 'updated_at', 'status', 'professor.pf_firstname' , 'professor.pf_lastname')
        ->where('classinfo.classes_id', $id)
        ->get();
    }

    //
}
