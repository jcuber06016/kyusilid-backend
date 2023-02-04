<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use PhpParser\Node\Expr\Cast\Object_;

class PersonlistController extends Controller
{
    //

    public function getpersonlist($id = null){
        return DB:: table('classlist')
        ->join('login' ,'login.acc_id' ,'=' ,'classlist.acc_id')     
        ->join('student' , 'student.acc_id' ,'=' ,'login.acc_id' ,'left outer')
        ->join('professor' ,'professor.acc_id' , '=', 'login.acc_id' , 'left outer')
        ->join('department', 'department.dep_id' , '=' ,'professor.dep_id', 'left outer') 
        ->select('login.usertype', 'login.acc_id' , 'login.firstname' , 'login.lastname' , 'login.middle' ,'login.suffix' , 'login.title' , 'classlist.classes_id' ,'student.stud_no', 'department.dep_name')
        ->where('classlist.classes_id', $id)
        ->get();    
    }

    public function getstudentlist($id = null){ // acc id 
        $classlist = DB:: table('classlist')
        ->join('classinfo' , 'classinfo.classes_id', '=' , 'classlist.classes_id')
        ->join('subject' , 'subject.sub_id' , '=' , 'classinfo.sub_id')
        ->join('days' , 'days.day_id' ,'=' ,'classinfo.day_id') 
        ->where('classlist.acc_id' , $id)
        ->where( 'classinfo.isarchived' , 0)
        ->select('classinfo.classes_id' , 'sub_name' , 'sub_code', 'day_label' , 'sched_from' , 'sched_to')
        ->get();

        $studentlist =[];


        foreach( $classlist as $classlistitem){
            $temp = DB:: table('classlist')
            ->join('login' ,'login.acc_id' ,'=' ,'classlist.acc_id')  
            ->where('classlist.classes_id' , $classlistitem->classes_id)
            ->where('login.usertype' , 'stud') ->get();
    
            $studentlist[] = [
                "classitem" => $classlistitem,
                "studentlist" => $temp
            ];          
        }

        return $studentlist;

       
    }

 


}
