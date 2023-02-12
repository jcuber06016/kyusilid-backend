<?php

namespace App\Http\Controllers;

use App\Models\Activities;
use App\Models\Activity_assign;
use App\Models\Activitycomments;
use App\Models\Topics;
use Carbon\Carbon;
use Hamcrest\Core\HasToString;
use Illuminate\Http\Request;
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




    public function createactivity(Request $request){

  
       $topic = $request->input('topic');
       $studentselection = $request->input('studentselection');
     

        foreach($studentselection as $element){
            $temp  = new Activities();
            $temp->activity_title = $request->input('title');
            $temp->activity_type = $request->input('activity_type'); 
            $temp->allow_edit = $request->input('allowedit');
            $temp->allow_late = $request->input('allowlate');
            $temp->availability  = $request->input('availability');




            $temp->category = 'lab';
            

            $gettopic =  DB::table('topic')
            ->where('classes_id' ,$element['classitem']['classes_id'])
            ->where('topic_name', $topic)
            ->select('topic_id')->first();
           
        
            if($gettopic !== null){
                $temp->topic_id = $gettopic->topic_id;
            }else{
                $temptopic = new Topics();
                $temptopic->topic_name = $topic;
                $temptopic->classes_id = $element['classitem']['classes_id'];
                $temptopic->save();
                $temp->topic_id = $temptopic->topic_id;   
            }
           
            $temp->save(); 


            $activity_id = $temp->topic_id;
            if($temp->activity_type !== "Material"){
                foreach($element['studentlist'] as $studentitem){

                    if($studentitem['selected'] == true){
                        $assign = new Activity_assign();
                        $assign-> acc_id = $studentitem['studentitem']['acc_id'];
                        $assign->activity_id = $activity_id;
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
        $temp -> acc_id = $request->input('acc_id');
        $temp->activity_id = $request->input('activity_id');
        $temp->date_posted = Carbon::now();
        $temp->comment_content = $request->input('comment_content');
        $temp->save();

        return DB:: table('activity_comment')
        ->join('login' , 'login.acc_id' , "=" , "activity_comment.acc_id")
        ->where('activity_id', $request->input('activity_id'))->get();
    }

    




    

        
}



    

