<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;

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

 


}
