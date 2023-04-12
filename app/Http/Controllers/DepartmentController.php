<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Adminlog;
use Carbon\Carbon;

class DepartmentController extends Controller
{
    function getdepartment($id= null){
        $depadminlist = DB::table('department')->join('admin' , 'admin.dep_id', "=" , 'department.dep_id')
        ->where('admin.acc_id' , $id)
        ->get();
        return $depadminlist;


    }
    
     function getclasslist2($id = null){  //id == department
     $temp = DB::table('classinfo')
     ->join('subject' , 'subject.sub_id' , "=" , 'classinfo.sub_id' )
     ->join('section' , 'section.sec_id' , "=" , "classinfo.sec_id" )
     ->join('department' , 'department.dep_id' , "=" , "classinfo.dep_id")
     ->join('login' , "login.acc_id" , '=' , 'classinfo.acc_id' , 'left outer')
     ->join('days' , 'days.day_id' , '=' , "classinfo.day_id" , )
     ->where('classinfo.dep_id' , $id)
      ->select("dep_name",
        "dep_code",
        "subject.yearlvl",
        "sched_from",
        "sched_to",
        "sub_code",
        "sub_name",
        "sem_id",
        "sec_name",
        'firstname',
        'lastname',
        'middle',
        'suffix',
        'classes_id',
        'title',
        'day_label',
        'classinfo.yearlvl'
        )
     ->get();
     
     
       $adminclasslist= [];



        foreach($temp as $tempitem){
            $studcount =DB::table('classlist')
            ->join('login' , "login.acc_id" , "=" , 'classlist.acc_id')
            ->where('classes_id' , $tempitem->classes_id)
            ->where('usertype' , "stud")
            ->count();
            $classlistentry = 
            [
                "classes_id" =>$tempitem->classes_id,
                "sub_name" => $tempitem->sub_name,
                "profname" => $tempitem->title . " " . $tempitem->firstname . " " . $tempitem->middle . " " . $tempitem->lastname .  " " . $tempitem->suffix,
                "yearsection" => $tempitem->dep_code . "-" . $tempitem->yearlvl . $tempitem->sec_name,
                "schedule" => $tempitem->day_label . ', ' . $tempitem->sched_from . '-' . $tempitem->sched_to,
                "studentcount" => $studcount,
                "yearlvl" => $tempitem->yearlvl
                
                
            ];

        $adminclasslist[] = $classlistentry;
 

        }

        return $adminclasslist;
     
     
    }
    
    
 
    function getsubjectlist($id = null) {     // id = department
        $temp = DB::table('subject')
        ->where('dep_id' , $id)->get();
        return $temp;
    }
    
    
    function accountlist($id = null) { //id is departamet
        $studentlist= DB::table('login')
        ->join('student' , 'student.acc_id' ,'=' , 'login.acc_id')
        ->where('student.dep_id', $id)->get();
        
        $studentlist2 = [];
        foreach($studentlist as $studitem){
            $studentlist2[] = [
                'name' => $studitem->firstname . ' ' . $studitem->middle. ' ' . $studitem->lastname,
                'studnum' => $studitem->stud_no ,
                'status' => $studitem->status,
                'active' => $studitem->active,
                'acc_id' => $studitem->acc_id
            ];
        }


        $proflist = DB::table('login')
        ->join('professor' , "professor.acc_id" , '=', 'login.acc_id')
        ->where('professor.dep_id' , $id)->get();

        $proflist2= [];

        foreach($proflist as $profitem){
            $proflist2[]= [
                'acc_id' => $profitem->acc_id,
                'name' =>$profitem->firstname . ' ' . $profitem->middle. ' ' . $profitem->lastname,
                'faculty_id' => $profitem->faculty_id,
                'active'=> $profitem->active
                
            ];
        }

        return [
            'studentlist' => $studentlist2,
            'proflist' =>$proflist2
        ];

    }
    
    
    function getadminlog(){
        $adminlog = DB::table('adminlog')
        ->join('login' , 'login.acc_id' , "=" , 'adminlog.acc_id')
        ->orderBy('log_id' , "DESC")
        ->get();
        return $adminlog;
    }
    
    
    function adminlog(Request  $request){
        $temp =  Carbon::now();
        $newlog = new Adminlog();
        $newlog->action = $request->input('action');
        $newlog->created_at = $temp->format('Y-m-d');
        $newlog->created_at_time = $temp->format('H:i:s');
        $newlog->acc_id = $request->input('acc_id');
        
        $newlog->save();
          
                        
       
        
        
    }

    

  

}
