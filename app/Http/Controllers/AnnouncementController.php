<?php

namespace App\Http\Controllers;

use App\Models\Announcementcomments;
use App\Models\AdminAnnouncement;
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
        ->select('an_title' ,'an_content','announcement.classes_id', 'created_at', 'schedule', 'login.lastname' , 'login.firstname' , 'login.middle' , 'login.suffix' , 'login.title' , 'login.acc_id' ,'announcement.an_id')
        ->where('classinfo.classes_id', $id)
        ->orderByDesc('an_id')
        ->get();

        $announcementlist =[];

        foreach( $temp as $tempitem){
            $temp2 = DB:: table('announcementcomments')
            ->join('login' , "login.acc_id" , "=", "announcementcomments.acc_id")
            ->select('login.firstname', 'login.middle' , 'login.lastname', 'login.title', 'login.suffix' ,'announcementcomments.date_posted' , 'announcementcomments.com_content' , 'announcementcomments.com_id' ,'login.acc_id')
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
            ->select('login.firstname', 'login.middle' , 'login.lastname', 'login.title', 'login.suffix' ,'announcementcomments.date_posted' , 'announcementcomments.com_content' , 'announcementcomments.com_id' ,'login.acc_id')
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
        $temp -> created_at = Carbon::now();
        $temp-> schedule = $request ->input('schedule');
        $temp-> save();
   

       
        return $temp;

        
    }

  


    public function getcomments($id){
        return DB:: table('announcementcomments')
        ->join('login' , "login.acc_id" , "=", "announcementcomments.acc_id")
        ->select('login.firstname', 'login.middle' , 'login.lastname', 'login.title', 'login.suffix' ,'announcementcomments.date_posted' , 'announcementcomments.com_content' , 'announcementcomments.com_id' ,'login.acc_id')
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

    
    public function deleteannouncement($id =null , $id2 = null){ //id == announcement id $id2 == class id
        $todelete = Announcements ::find($id);
        $temp2 = $todelete -> classes_id;
        $todelete-> delete();
        
        
      
        $temp = DB::table('announcement')
        ->join('classinfo' , 'classinfo.classes_id' , "=", 'announcement.classes_id')
        ->join('login' , 'login.acc_id' , "=", 'announcement.acc_id')
        ->select('an_title' ,'an_content', 'created_at', 'announcement.classes_id', 'schedule', 'login.lastname' , 'login.firstname' , 'login.middle' , 'login.suffix' , 'login.title' , 'login.acc_id' ,'announcement.an_id')
        ->where('classinfo.classes_id', $temp2)
        ->orderByDesc('schedule')
        ->get();

        $announcementlist =[];

        foreach( $temp as $tempitem){
            $temp21 = DB:: table('announcementcomments')
            ->join('login' , "login.acc_id" , "=", "announcementcomments.acc_id")
            ->select('login.firstname', 'login.middle' , 'login.lastname', 'login.title', 'login.suffix' ,'announcementcomments.date_posted' , 'announcementcomments.com_content' , 'announcementcomments.com_id' ,'login.acc_id')
            ->where('announcementcomments.an_id' , $tempitem->an_id)
            ->orderBy('announcementcomments.date_posted')
            ->get();
    
            $announcementlist[] = [
                "announcementitem" => $tempitem,
                "commentlist" => $temp21
            ];
        }

        return $announcementlist;


    }


    public function editannouncement(Request  $request){
        $newannouncement = Announcements::find($request->input('an_id'));
        
        $newannouncement->an_title = $request->input('an_title');
        $newannouncement->an_content = $request->input('an_content');
        $newannouncement->update();

        return $newannouncement;

        $temp = DB::table('announcement')
        ->join('classinfo' , 'classinfo.classes_id' , "=", 'announcement.classes_id')
        ->join('login' , 'login.acc_id' , "=", 'announcement.acc_id')
        ->select('an_title' ,'an_content','announcement.classes_id', 'created_at', 'schedule', 'login.lastname' , 'login.firstname' , 'login.middle' , 'login.suffix' , 'login.title' , 'login.acc_id' ,'announcement.an_id')
        ->where('classinfo.classes_id', $newannouncement->classes_id)
        ->orderByDesc('schedule')
        ->get();

        $announcementlist =[];

        foreach( $temp as $tempitem){
            $temp2 = DB:: table('announcementcomments')
            ->join('login' , "login.acc_id" , "=", "announcementcomments.acc_id")
            ->select('login.firstname', 'login.middle' , 'login.lastname', 'login.title', 'login.suffix' ,'announcementcomments.date_posted' , 'announcementcomments.com_content' )
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
    
    
    
    
    
    public function getadminannouncement($id = null){
        $temp = DB:: table('admin_ann')
        ->join('login' , "login.acc_id" , "=", "admin_ann.acc_id")
        ->where('dep_id' , $id)->get();
        return $temp;
    }

    public function createadminannouncement(Request $request){
        $newannouncement = new AdminAnnouncement();
        $newannouncement->dep_id = $request->input('dep_id');
        $newannouncement->acc_id= $request->input('acc_id');
        $newannouncement->announcement_content = $request->input('announcement_content');
        $newannouncement->created_at= Carbon::now();
        $newannouncement->save();


    }


    public function deletadminannouncement($id = null){
        $delete_ann = AdminAnnouncement::find($id);
        if($delete_ann != null){
          $delete_ann->delete();
        }
     
    }


public function deleteannouncementcomment($id = null){
        $delete_ann_comment = Announcementcomments::find($id);
        if($delete_ann_comment != null){
             
                 $delete_ann_comment->delete();
        }
       
    }
    
    
  





    



    //
}
