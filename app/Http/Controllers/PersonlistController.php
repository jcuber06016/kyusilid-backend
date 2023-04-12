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
    
    
    
    public function getgradelist($id =null){
        $studlist = DB::table('classlist')
            ->join('login' ,'login.acc_id' ,'=' ,'classlist.acc_id')    
            ->where('classlist.classes_id', $id)
            ->where('login.usertype' , "stud")
           ->select("firstname" ,  "lastname" , "middle" , "suffix" , "login.acc_id")
            ->get();
            
         
         
         
        $gradelist2 = []; 
        foreach($studlist as $item){
            $activities = DB::table('activity_assign')
            ->join("activity" , 'activity.activity_id' , "=" , "activity_assign.activity_id")
            ->where("activity_assign.acc_id" , $item->acc_id)
            ->where("activity_type" , "Activity")
            ->select( "grade" , "points")
            ->get();
            $activitygrade =[
                    "grade" => 0,
                    "points" => 0
                   ];
            $activitygradetotal = 0;
            $activitypointstotal = 0;
      
            if(count($activities) > 0){
            
                foreach($activities as $item2){
                    $activitygradetotal +=  $item2->grade;
                    $activitypointstotal += $item2->points;
                   
                } 
               $activitygrade = [
                    "grade" => $activitygradetotal / count($activities),
                    "points" => $activitypointstotal / count($activities)
                   ];
                   
                   
            }
            
            
             $assignment = DB::table('activity_assign')
            ->join("activity" , 'activity.activity_id' , "=" , "activity_assign.activity_id")
            ->where("activity_assign.acc_id" , $item->acc_id)
            ->where("activity_type" , "Assignment")
            ->select( "grade" , "points")
            ->get();
            $assignmentgrade = [
                    "grade" => 0,
                    "points" => 0
                   ];
            $assignmentgradetotal = 0;
            
      
            if(count($assignment)>0){
                  $temp = 0;
                foreach($assignment as $item2){
                    $assignmentgradetotal +=  $item2->grade;
                     $temp = $item2->points;
                   
                } 
               $assignmentgrade =[
                    "grade" => $assignmentgradetotal / count($assignment),
                    "points" => $temp
                   ];
            }
            
            ////
             $questionnaire = DB::table('activity_assign')
            ->join("activity" , 'activity.activity_id' , "=" , "activity_assign.activity_id")
            ->where("activity_assign.acc_id" , $item->acc_id)
            ->where("activity_type" , "Questionnaire")
            ->select( "grade" , "points")
            ->get();
            $questionnairegrade = [
                    "grade" => 0,
                    "points" => 0
                   ];
            $questionnairegradetotal = 0;
            
      
            if(count($questionnaire)>0){
                  $temp = 0;
                foreach($questionnaire as $item2){
                    $questionnairegradetotal +=  $item2->grade;
                     $temp = $item2->points;
                   
                } 
               $questionnairegrade =[
                    "grade" => $questionnairegradetotal / count($questionnaire),
                    "points" => $temp
                   ];
            }
            
            
            ////
            
   
            
            $temp = [
                "student" => [
                        "name" => $item->firstname . " " . $item->middle . " " . $item->lastname . " " . $item->suffix,
                        "acc_id" => $item->acc_id
                    ],
                "activity" => $activitygrade,
                "assignment" => $assignmentgrade,
                "questionnaire" => $questionnairegrade
            ];
            
            $gradelist2[] = $temp;
        
        }
            
        return $gradelist2;
            
            
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


    function getdeptinfo($id = null){
        $admintable = DB:: table('admin')
        ->join('login' , 'login.acc_id' , "=", "admin.acc_id")
        ->where('admin.dep_id' , $id)->get();

        $firstyear = DB::table('classinfo')
        ->where('dep_id' , $id)
        ->whereNotNull('classinfo.sec_id')
        ->whereNotNull('classinfo.sub_id')
        ->where('yearlvl' , 1)->count();

        $secondyear = DB::table('classinfo')
        ->where('dep_id' , $id)
        ->whereNotNull('classinfo.sec_id')
        ->whereNotNull('classinfo.sub_id')
        ->where('yearlvl' , 2)->count();

        $thirdyear = DB::table('classinfo')
        ->where('dep_id' , $id)
        ->whereNotNull('classinfo.sec_id')
        ->whereNotNull('classinfo.sub_id')
        ->where('yearlvl' , 3)->count();

        $fourthyear = DB::table('classinfo')
        ->where('dep_id' , $id)
        ->whereNotNull('classinfo.sec_id')
        ->whereNotNull('classinfo.sub_id')
        ->where('yearlvl' , 4)->count();


        $studcount = DB:: table('student')
        ->where('stud_course' , $id)->count();

        $profcount = DB:: table('professor')
        ->where('dep_id' , $id)->count();

        $temp2 = [
            'depadminlist' => $admintable,
            'firstyear' =>$firstyear, 
            'secondyear' => $secondyear,
            'thirdyear' => $thirdyear,
            'fourthyear' => $fourthyear,
            'studcount' => $studcount,
            'profcount' => $profcount
        ];


        return $temp2;

    }
    
    
    
    
    
    
  

 


}
