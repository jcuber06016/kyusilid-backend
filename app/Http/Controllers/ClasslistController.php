<?php

namespace App\Http\Controllers;


use Illuminate\Support\Facades\DB;
use Symfony\Component\CssSelector\Node\FunctionNode;

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
                        'classinfo.sessionname1',
                        'classinfo.sessionname2',
                        DB::raw("DATE_FORMAT(classinfo.sched_from, '%h:%i %p') as sched_from"),
                        DB::raw("DATE_FORMAT(classinfo.sched_to, '%h:%i %p') as sched_to"),
                        'classinfo.sched_from2',
                        'classinfo.sched_to2',
                        'classinfo.yearlvl', 
                        'section.sec_name' ,
                        'subject.sub_code' , 
                        'classinfo.classes_id' , 
                        'login.acc_id' , 
                        'classinfo.classbanner',
                        'isarchived',
                        'login.usertype')
            ->where('classlist.acc_id', $id)
            ->where('classinfo.isarchived' , 0)
            ->get();
   
            }

    public function checkusertype($id= null){
        $temp = DB::table('login')->where('login.acc_id', $id)->first();
        return $temp;    
    }


    public function getclasslist_archived($id = null){
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
                        'classinfo.sessionname1',
                        'classinfo.sessionname2',
                        'classinfo.sched_from' , 
                        'classinfo.sched_to' , 
                        'classinfo.sched_from2',
                        'classinfo.sched_to2',
                        'classinfo.yearlvl', 
                        'section.sec_name' ,
                        'subject.sub_code' , 
                        'classinfo.classes_id' , 
                        'login.acc_id' , 
                        'classinfo.classbanner',
                        'isarchived',
                        'login.usertype')
            ->where('classlist.acc_id', $id)
            ->where('classinfo.isarchived' , 1)
            ->get();
    }

 
}


     
