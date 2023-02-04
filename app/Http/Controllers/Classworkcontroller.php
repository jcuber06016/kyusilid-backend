<?php

namespace App\Http\Controllers;

use App\Models\Topics;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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



    
}
