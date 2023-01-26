<?php

namespace App\Http\Controllers;

use App\Models\Classinfo;
use Illuminate\Http\Request;
use App\Models\Classlist;
use Spatie\FlareClient\Api;
use Illuminate\Support\Facades\DB;

class ClasslistController extends Controller
{
    public function getclasslist($id = null){

        $temp = DB::table('login')->where('login.acc_id', $id)->first();
        $temp2 = $temp->usertype==='stud' ? 'classlist.acc_id' : 'classinfo.acc_id'; 

        
            return DB::table('classlist')
            ->join('classinfo', 'classinfo.classes_id' , "=" , 'classlist.classes_id')
            ->join('subject' , 'subject.sub_id' ,"=" ,"classinfo.sub_id" )
            ->join('professor' , 'professor.acc_id', "=", "classinfo.acc_id")
            ->join('days', 'classinfo.day_id' ,'=', 'days.day_id')
            ->join('section' , 'section.sec_id' , "=", 'classinfo.sec_id')
            ->join('login' , 'login.acc_id' , "=", $temp2)
            ->select('days.day_label' , 'professor.pf_firstname' , 'professor.pf_lastname' , 'subject.sub_name' ,'classinfo.sched_from' , 'classinfo.sched_to' , 'classlist.yr_id', 'section.sec_name' ,'subject.sub_code' , 'classinfo.classes_id' , 'login.acc_id' , 'login.usertype')
            ->where('login.acc_id', $id)
            ->get();


   
}

    public function checkusertype($id= null){
        $temp = DB::table('login')->where('login.acc_id', $id)->first();
        return $temp;

    
    }
}


     
