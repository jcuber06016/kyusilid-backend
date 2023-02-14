<?php

namespace App\Http\Controllers;

use App\Models\Activities;
use App\Models\Activity_assign;
use App\Models\Activitycomments;
use App\Models\Classlog;
use App\Models\Topics;
use Carbon\Carbon;
use Hamcrest\Core\HasToString;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\RequestStack;

class Classworkcontroller extends Controller
{


    public function gettopics($id =null){
        return DB:: table('topic')
        ->where('topic.classes_id' , $id)
        ->orderByDesc('topic.topic_id') 
        ->get();
    
    }

    public function getactivities($id= null){
        return DB:: table('activity')
        ->join('topic' ,'topic.topic_id' ,'=' ,'activity.topic_id')
        ->where('activity.topic_id' , $id)
        ->orderByDesc('activity.activity_id')
        ->get();
    }

   

    public function getcommentcount($id = null){
        return DB:: table('activity_comment')
        ->where('activity_comment.activity_id' , $id)
        ->count();
    }

    public function createtopic(Request $request){
        $temp = new Topics();
        $temp->topic_name = $request->input('topic_name');
        $temp->classes_id = $request->input('classes_id');
        $temp->save();
        return $temp;

    }

  

    public function getclass_log($id = null){
        return DB::table('class_log')
        ->join('login' , 'login.acc_id' ,'=', 'class_log.acc_id')
        ->join('activity' , 'activity.activity_id' , "=", 'class_log.activity_id')
        ->join('topic' ,'topic.topic_id' ,"=" ,"activity.topic_id") 
        ->select('firstname' , 
                'lastname' , 
                'title', 
                'suffix', 
                'log_type' , 
                'class_log.created_at' , 
                'activity.activity_id',              
                 "activity_title",
                 'activity.category',
                 "topic_name"
                 ,'activity_type'
                       
                 )
        ->where('class_log.classes_id' , $id)
        ->orderByDesc('class_log.classlog_id')
        ->get();
    }




    public function createactivity(Request $request){

        $createdby =$request->input('created_by');
        $postedby = $request->input('posted_by');
       $topic = $request->input('topic');
       $posttype = $request->input ('postschedtype');
       $schedule = $request ->input('schedule');
       $description = $request ->input('description');
       $schedoffset = $request->input('scheduleoffset');
       $duedate = $request->input('duedate');
       $activitytype = $request->input('activity_type');
       $studentselection = $request->input('studentselection');
        $days = array("Sunday" =>0 , "Monday"=>1 , "Tuesday" => 2, "Wednesday"=> 3, "Thursday" => 4 , "Friday"=>5 , "Saturday"=>7);
    

    
        foreach($studentselection as $element){
          
             if($element['selected'] != 1){
                continue;
             }
            


            $newactivity  = new Activities();
            $newactivity->activity_title = $request->input('title');
            $newactivity->activity_type = $activitytype; 
            $newactivity->allow_edit = $request->input('allowedit');
            $newactivity->allow_late = $request->input('allowlate');
            $newactivity->availability  = $request->input('availability');
            $newactivity->category = $request->input('category');
            $newactivity->description = $description;
            $newactivity->postedby = $postedby;
            $newactivity->createdby = $createdby;
  
        
            $gettopic =  DB::table('topic')
            ->where('classes_id' ,$element['classitem']['classes_id'])
            ->where('topic_name', $topic)
            ->select('topic_id')->first();

            if($gettopic !== null){
                $newactivity->topic_id = $gettopic->topic_id;
            
            }else{
                $temptopic = new Topics();
                $temptopic->topic_name = $topic;
                $temptopic->classes_id = $element['classitem']['classes_id'];
                $temptopic->save();
                $newactivity->topic_id = $temptopic->topic_id; 

            }


            

         

            if($posttype ==='fixed'){
               
              
                $newactivity->date_schedule =  Carbon::createFromFormat('Y-m-d\TH:i', $schedule);
           
               
            }else{
                   //get how many days till the next schedule
                $tt = $element['classitem']['day_label'];
                $targetday = $days[$tt];

                $dayoffset = 0 ;
                list($hour, $minute, $second) = explode(':', $element['classitem']['sched_from']);
               

                $targetschedule = Carbon::today();


                if($targetday > $targetschedule->dayOfWeek){
                    $dayoffset = $targetday - $targetschedule->dayOfWeek;
                }else{
                    $dayoffset = $targetday - $$targetschedule->dayOfWeek + 7;
                }
                $targetschedule->addDays($dayoffset);             
                $targetschedule->setTime((int) $hour, (int) $minute + $schedoffset , (int) $second);
                $newactivity->date_schedule = $targetschedule;    
                              
            }

            
            



            if($activitytype ==="Activity" || $activitytype ==="Assignment" ||$activitytype ==="Questionnaire"){
                $targetdue = clone $newactivity->date_schedule;
              
              //  return $targetdue;
                switch($duedate){
                case "none": 
                  
                    break;
                case 30 : 
                  $targetdue->addMinutes(30);
                  $newactivity->date_due = $targetdue; 
                    break;
                case 60 :              
                    $targetdue->addHours(1);              
                    $newactivity->date_due = $targetdue; ;    
                    break;
                case 'nextweek' :               
                    $targetdue->addDays(7);              
                    $newactivity->date_due = $targetdue;       
                    break;
                default:  
                    list($hour2, $minute2, $second2) = explode(':', $element['classitem']['sched_to']);
                    $targetdue->setTime((int) $hour2, (int) $minute2, (int) $second2);
                 
                    $newactivity->date_due = $targetdue; 
                    break;
                }   
                 
            }
         
            $newactivity->save(); 

            $newlog = new Classlog();
            $newlog-> acc_id = $postedby;
            $newlog->activity_id = $newactivity->activity_id;
            $newlog->classes_id = $element['classitem']['classes_id'];
            $newlog->created_at= Carbon::now();
            $newlog->log_type = "activity";
            $newlog->save();


          
            if($newactivity->activity_type !== "Material"){
                foreach($element['studentlist'] as $studentitem){
                // assigning activity to
                    if($studentitem['selected'] == true){
                        $assign = new Activity_assign();
                        $assign-> acc_id = $studentitem['studentitem']['acc_id'];
                        $assign->activity_id = $newactivity->activity_id;
                        $assign->save();
                    }             
                }
            }

        }      

    }

    public function getactivitycommentlist($id = null){
       return DB:: table('activity_comment')
       ->join('login' , 'login.acc_id' , "=" , "activity_comment.acc_id")
       ->where('activity_id', $id)->get();      
    }

    public function createactivitycomment(Request $request){
        $temp = new Activitycomments();
        $temp-> acc_id = $request->input('acc_id');
        $temp->activity_id = $request->input('activity_id');
        $temp->date_posted = Carbon::now();
        $temp->comment_content = $request->input('comment_content');
        $temp->save();


        $searchclass = DB::table('activity')
        ->join('topic' , 'topic.topic_id' , "=", "activity.topic_id")
        ->join('classinfo', 'classinfo.classes_id', "=" ,"topic.classes_id")
        ->where("activity_id" , $request->input('activity_id'))
        ->select('classinfo.classes_id')
        ->first(); 


        $newlog = new Classlog();
        $newlog-> acc_id = $request->input('acc_id');
        $newlog->activity_id = $request->input('activity_id');
        $newlog->classes_id = $searchclass->classes_id;
        $newlog->created_at= Carbon::now();
        $newlog->comment_id  = $temp->comment_id;
        $newlog->log_type=  "comment";
        $newlog->save();


        return DB:: table('activity_comment')
        ->join('login' , 'login.acc_id' , "=" , "activity_comment.acc_id")
        ->where('activity_id', $request->input('activity_id'))->get();
    }

    


   
        
}



    

