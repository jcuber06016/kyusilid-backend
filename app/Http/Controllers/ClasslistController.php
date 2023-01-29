<?php

namespace App\Http\Controllers;


use Illuminate\Support\Facades\DB;

class ClasslistController extends Controller
{
    public function getclasslist($id = null){

       
            return DB::table('classlist')
            ->join('classinfo', 'classinfo.classes_id' , "=" , 'classlist.classes_id')
            ->join('subject' , 'subject.sub_id' ,"=" ,"classinfo.sub_id" )
            ->join('professor' , 'professor.acc_id', "=", "classinfo.acc_id")
            ->join('days', 'classinfo.day_id' ,'=', 'days.day_id')
            ->join('section' , 'section.sec_id' , "=", 'classinfo.sec_id')
            ->join('login' , 'login.acc_id' , "=", 'classinfo.acc_id' )
            ->select('days.day_label' , 
                        'login.firstname' , 
                        'login.lastname',
                        'login.middle',
                        'login.suffix', 
                        'login.title',
                        'subject.sub_name' ,
                        'classinfo.sched_from' , 
                        'classinfo.sched_to' , 
                        'classinfo.yearlvl', 
                        'section.sec_name' ,
                        'subject.sub_code' , 
                        'classinfo.classes_id' , 
                        'login.acc_id' , 
                        'classinfo.classbanner',
                        'isarchived',
                        'login.usertype')
            ->where('classlist.acc_id', $id)
            ->get();


   
}

    public function checkusertype($id= null){
        $temp = DB::table('login')->where('login.acc_id', $id)->first();
        return $temp;

    
    }
}


     
