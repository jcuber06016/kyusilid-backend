<?php

namespace App\Http\Controllers;

use App\Models\Announcementcomments;
use App\Models\Announcements;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

use Illuminate\Http\Request;

class AnnouncementController extends Controller
{
    public function getannouncement($id = null){
        $temp = DB::table('announcement')
        ->join('classinfo' , 'classinfo.classes_id' , "=", 'announcement.classes_id')
        ->join('login' , 'login.acc_id' , "=", 'announcement.acc_id')
        ->select('an_title' ,'an_content', 'created_at', 'schedule', 'login.lastname' , 'login.firstname' , 'login.middle' , 'login.suffix' , 'login.title' , 'login.acc_id' ,'announcement.an_id')
        ->where('classinfo.classes_id', $id)
        ->orderByDesc('schedule')
        ->get();

        $announcementlist =[];

        foreach( $temp as $tempitem){
            $temp2 = DB:: table('announcementcomments')
            ->join('login' , "login.acc_id" , "=", "announcementcomments.acc_id")
            ->select('login.firstname', 'login.middle' , 'login.lastname', 'login.title', 'login.suffix' ,'announcementcomments.date_posted' , 'announcementcomments.com_content')
            ->where('announcementcomments.an_id' , $tempitem->an_id)
            ->orderBy('announcementcomments.date_posted')
            ->get();
    
            $announcementlist[] = [
                "announcementitem" => $tempitem,
                "commentlist" => $temp2
            ];
        }

        return $announcementlist;

       

       
    }

    public function getannouncementforstudent($id = null){
        $temp=  DB::table('announcement')
        ->join('classinfo' , 'classinfo.classes_id' , "=", 'announcement.classes_id')
        ->join('login' , 'login.acc_id' , "=", 'announcement.acc_id')
        ->select('an_title' ,'an_content', 'created_at', 'schedule', 'login.lastname' , 'login.firstname' , 'login.middle' , 'login.suffix' , 'login.title' , 'login.acc_id' ,'announcement.an_id')
        ->where('classinfo.classes_id', $id)
        ->where('schedule' , '<=', Carbon::now())
        ->orderByDesc('schedule')
        ->get();


        $announcementlist =[];

        foreach( $temp as $tempitem){
            $temp2 = DB:: table('announcementcomments')
            ->join('login' , "login.acc_id" , "=", "announcementcomments.acc_id")
            ->select('login.firstname', 'login.middle' , 'login.lastname', 'login.title', 'login.suffix' ,'announcementcomments.date_posted' , 'announcementcomments.com_content')
            ->where('announcementcomments.an_id' , $tempitem->an_id)
            ->orderBy('announcementcomments.date_posted')
            ->get();
    
            $announcementlist[] = [
                "announcementitem" => $tempitem,
                "commentlist" => $temp2
            ];
        }

        return $announcementlist;
        
    }



   

    public function addannouncement(Request $request){
        $temp = new Announcements();
        $temp -> an_title = $request -> input('an_title');
        $temp -> an_content = $request ->input('an_content');
        $temp -> classes_id = $request ->input('classes_id');
        $temp -> acc_id = $request ->input('acc_id');
        $temp -> created_at = $request->input('created_at');
        $temp-> schedule = $request ->input('schedule');
        $temp-> save();
   

       
        return $temp;

        
    }

  


    public function getcomments($id){
        return DB:: table('announcementcomments')
        ->join('login' , "login.acc_id" , "=", "announcementcomments.acc_id")
        ->select('login.firstname', 'login.middle' , 'login.lastname', 'login.title', 'login.suffix' ,'announcementcomments.date_posted' , 'announcementcomments.com_content')
        ->where('announcementcomments.an_id' , $id)
        ->get();
    }


    public function postcomment(Request $request){
        $temp = new Announcementcomments();
        $temp ->an_id = $request->input('an_id');
        $temp ->acc_id = $request -> input('acc_id');
        $temp ->com_content = $request->input('com_content');
        $temp->date_posted = Carbon:: now();
        $temp->save();

    

    }

    public function announcementpostnow(Request $request){
        $announcement = Announcements::find( $request->input('an_id'));
     
       
        if($announcement  != null){
        
            $announcement->schedule = Carbon::now();
            $announcement->update();
        }

        return $announcement;



    }

    //
}
