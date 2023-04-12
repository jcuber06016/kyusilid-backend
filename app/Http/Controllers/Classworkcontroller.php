<?php

namespace App\Http\Controllers;

use App\Models\Activities;
use App\Models\Activity_assign;
use App\Models\Activitycomments;
use App\Models\Announcements;
use App\Models\Announcementcomments;
use App\Models\Attendance;
use App\Models\Classlog;
use App\Models\Topics;
use Carbon\Carbon;
use Hamcrest\Core\HasToString;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\RequestStack;
use Illuminate\Support\Facades\Storage;

class Classworkcontroller extends Controller
{


    public function gettopics($id =null){
        
        $temp = DB::table('classinfo')
        ->join('subject' , 'subject.sub_id' , "=", 'classinfo.sub_id')
        ->where('classes_id' , $id)
        ->select('sub_name')
        ->first();
        
        $temp2 = DB::table('topic')
        ->where('topic.classes_id' , $id)
        ->orderByDesc('topic.topic_id') 
        ->get();
        
        $temp3 = [
            "classinfo" => $temp->sub_name,
            "topiclist" => $temp2
            ];
    
    
        return $temp3;
    }
    
        public function gettopicsbysubject($id= null){
        $classtempid = DB::table("classinfo")
        ->where("sec_id", null)
        ->where('sub_id' , $id)
        ->select('classes_id')
        ->first();
        
           $temp = DB::table('classinfo')
        ->join('subject' , 'subject.sub_id' , "=", 'classinfo.sub_id')
        ->where('classes_id' , $classtempid->classes_id)
        ->select('sub_name' , 'classes_id')
        ->first();
        
        $temp2 = DB::table('topic')
        ->where('topic.classes_id' , $temp->classes_id)
        ->orderByDesc('topic.topic_id') 
        ->get();
        
        $temp3 = [
            "classinfo" => $temp->sub_name,
            "topiclist" => $temp2
            ];
    
    
        return $temp3;
        
        
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
            $newactivity->points = $request->input('points');
            $newactivity->file_link = $request->input('file_link');
            
            if($request->input('category')!= null ){
                $newactivity->category = $request->input('category');
            }else{
                $newactivity->category = "Lecture";
            }
        
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
            // if ($request->hasFile('file_link')){
            //         $file = $request->file('file_link');
            //         $path = Storage::disk('public')->putFile('uploads', $file);
            //         $url = Storage::url($path);
            //         $newactivity->file_link = $url;
            // }
            

            
         
            $newactivity->save(); 

        //For class log
        
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
    
    
    public function postactivity(Request $request){
        
        //get the topic in that class
        
        $thattopic = DB::table('topic')->where('classes_id' , $request->input('topic_id'))->select('topic_id')->first();
        $temptopic_id = 0;
      
        
        if($thattopic!= null){
                $temptopic_id = $thattopic->topic_id;
                $newactivity = new Activities();
                $newactivity->topic_id= $temptopic_id;
                $newactivity->activity_title = $request->input('activity_title');
                $newactivity->description = $request->input('description');
                $newactivity->activity_type = $request->input('activity_type');
                $newactivity->category = $request->input('category');
                $newactivity->createdby= $request->input('createdby');
                $newactivity->file_link = $request->input('file_link');
                $newactivity->quiz_link = $request->input('quiz_link'); 
                $newactivity->ifmodule = true;
                $newactivity->save();
            
                
        }
    
     
    }
    
public function uploadfile(Request $request)
{
    if ($request->hasFile('file_link') && $request->file('file_link')->isValid()) {
        $file = $request->file('file_link');
        $path = Storage::disk('public')->putFile('uploads', $file);
        $url = Storage::url($path);
        $fileData = [
            'name' => $file->getClientOriginalName(),
            'type' => $file->getClientMimeType(),
            'size' => $file->getSize()
        ];
       
        return response()->json([
            'message' => 'File uploaded successfully.',
            'url' => $url,
            'path' => $path,
            'data' => $fileData
        ]);
    } else {
        return response()->json([
            'message' => 'Please upload a file.'
        ], 400);
    }
}



    public function posttomodule(Request $request){

       
   
       
       $newactivity  = new Activities();
       
           $newactivity->activity_title = $request->input('activity_title');
            $newactivity->activity_type = $request->input('activity_type');

            $newactivity->category = $request->input('category');
            $newactivity->description = $request->input('description');
            $newactivity->postedby = $request->input('posted_by');
            $newactivity->createdby = $request->input('created_by');
            
             $gettopic =  DB::table('topic')
            ->where('classes_id' ,$request->input('classes_id'))
            ->where('topic_name', $request->input('topic_name'))
            ->select('topic_id')->first();

            if($gettopic !== null){
                $newactivity->topic_id = $gettopic->topic_id;
            
            }else{
                $temptopic = new Topics();
                $temptopic->topic_name = $request->input('topic_name');
                $temptopic->classes_id = $request->input('classes_id');
                $temptopic->save();
                $newactivity->topic_id = $temptopic->topic_id; 
            }
            $newactivity->save();
             
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


    public function getassignedactivities($id= null , $id2 = null){ //id = tpoic  id 2 = acc id



        return DB::table('activity_assign')
        ->join('activity' , 'activity.activity_id' , '=' , 'activity_assign.activity_id')
        ->where('activity.topic_id' , $id)
        ->where('activity_assign.acc_id' , '=' , $id2)
        ->get();

    
    }



    public function getattendance($id = null) { // id = attendance assign id
            $attendance = Attendance::find($id);
            $attendance->status = "present";


    }
    
     public function updateactivity(Request $request){
       $updatedactivity = Activities::find($request->input('activity_id'));
       $updatedactivity->activity_title= $request->input('activity_title');
       $updatedactivity->description = $request->input('description');
       $updatedactivity->update();
    }
    
    
       public function deleteactivity($id = null){
        $del_activity = Activities::find($id);
        if($del_activity !== null){
            $del_activity->delete();
        }
    }
    
    public function deleteactivitycomment($id = null){
        $del_comment = Activitycomments::find($id);
        if($del_comment != null){
            $del_comment->delete();
            return "deleted";
        }else{
            return "null";
        }
    }
    
    
    
    
    public function admincreatemodule(Request $request){
        $subjecttemp = DB::table('subject')
        ->where('sub_code' , $request->input('sub_code'))
        ->select('sub_id')
        ->first();
        
        
        
        $classinfo = DB::table('classinfo')
        ->where('sec_id' , null)
        ->where('sub_id' , $subjecttemp->sub_id)
        ->select('classes_id')
        ->first();
        
        $topic = DB::table("topic")
        ->where('classes_id' , $classinfo->classes_id )
        ->where('topic_name' , $request->input('topic_name'))
        ->select('topic_id')
        ->first();
        
        $temptopic = null;
        
        if($topic != null){
            $temptopic = $topic->topic_id;
        }else{
            $newtopic = new Topics();
            $newtopic->classes_id = $classinfo->classes_id;
            $newtopic->topic_name = $request->input('topic_name');
            $newtopic->save();
            $temptopic = $newtopic->topic_id;
            
        }
        
        //setting other activity data
        $newactivity = new Activities();
        $newactivity->title = $request->input('title');
        $newactivity->created_by = $request->input('created_by');
        $newactivity->description =  $request->input('description');
        $newactivity->activity_type =  $request->input('activity_type');
        $newactivity->topic_id =  $request->input('topic_id');
        $newactivity->questionnaire_link = $request->input('questionnaire_link');
        $newactivity->save();
        
        
    }
    
    
    public function gettopicstring($id = null){
           $classtempid = DB::table("classinfo")
        ->where("sec_id", null)
        ->where('sub_id' , $id)
        ->select('classes_id')
        ->first();
        
           $temp = DB::table('classinfo')
        ->join('subject' , 'subject.sub_id' , "=", 'classinfo.sub_id')
        ->where('classes_id' , $classtempid->classes_id)
        ->select('sub_name' , 'classes_id')
        ->first();
        
        $temp2 = DB::table('topic')
        ->where('topic.classes_id' , $temp->classes_id)
        ->select('topic_name' , 'topic_id')
        ->get();
        
      
    
    
        return $temp2;
        
    }
    
    
    
    public function admincreatemodule2(Request $request){
        $newactivity = new Activities();
        $newactivity->activity_title = $request->input('title');
        $newactivity->description = $request->input('description');
        $newactivity->topic_id= $request->input('topic_id');
        $newactivity->createdby = $request->input('created_by');
        $newactivity->category = $request->input('category');
        $newactivity->activity_type = $request->input('activity_type');
        $newactivity->save();
        
        
    }
    
    
    public function getprofilestatus($id = null){
        $classlist = DB::table('classlist')
        ->where('classlist.acc_id' , $id)
        ->join('classinfo' ,'classinfo.classes_id' ,'=', 'classlist.classes_id')
        ->join('subject' , 'subject.sub_id' , '=' , 'classinfo.sub_id')
        ->select('sub_name' , 'classlist.classes_id')->get();
        
        
        $classlisttemp= [];
        $pendinglistcount =[];
        $donelistcount = [];
        $missinglistcount = [];
        
        
    
        foreach($classlist as $classlistitem){
            
            
            
            
            $pendingcount= DB::table('activity_assign')
            ->join('activity' , 'activity.activity_id' , '=' ,'activity_assign.activity_id')
            ->join('topic' , 'topic.topic_id' , '=' , 'activity.topic_id')
            ->join('classinfo' , 'classinfo.classes_id' , '=' , 'topic.classes_id')
            ->where('activity_assign.acc_id' , $id)
            ->where('classinfo.classes_id' , $classlistitem->classes_id)
            ->where('activity_assign.status' , "pending")
            ->count();
            
           $donecount= DB::table('activity_assign')
            ->join('activity' , 'activity.activity_id' , '=' ,'activity_assign.activity_id')
            ->join('topic' , 'topic.topic_id' , '=' , 'activity.topic_id')
            ->join('classinfo' , 'classinfo.classes_id' , '=' , 'topic.classes_id')
            ->where('activity_assign.acc_id' , $id)
            ->where('classinfo.classes_id' , $classlistitem->classes_id)
            ->where('activity_assign.status' , "done")
            ->count();
            
             $missingcount= DB::table('activity_assign')
            ->join('activity' , 'activity.activity_id' , '=' ,'activity_assign.activity_id')
            ->join('topic' , 'topic.topic_id' , '=' , 'activity.topic_id')
            ->join('classinfo' , 'classinfo.classes_id' , '=' , 'topic.classes_id')
            ->where('activity_assign.acc_id' , $id)
            ->where('classinfo.classes_id' , $classlistitem->classes_id)
            ->where('activity_assign.status' , "missing")
            ->count();
            
            $pendinglistcount[] = $pendingcount;
            $donelistcount[] = $donecount;
            $missinglistcount[] = $missingcount;
            $classlisttemp[]= $classlistitem->sub_name;
            
            
        }
        
        return [
            "classlist" => $classlisttemp,
            "pendinglist" => $pendinglistcount,
            "donelist" => $donelistcount,
            "missinglist" => $missinglistcount
        ];
    }
    
    
    public function getactivitystatus($actId = null, $accId = null){
        
        if($actId != null && $accId != null){
            $activitystatus  = DB::table('activity_assign')->where('activity_assign.activity_id' , $actId)->where('acc_id' ,  $accId)
            ->join('activity' , 'activity.activity_id' , '=' , 'activity_assign.activity_id')
            ->select('status' , 'grade' , 'points' , 'assign_id')
            ->first();
        
            if($activitystatus != null){
                
                return $activitystatus;
            }else{
                return "unassigned";
            }
        }
    }
    
    
    public function getactivityresponses($id= null){
        
        if($id != null ){
            $responses = DB::table( 'activity_assign')
            ->where('activity_assign.activity_id' , $id)
            ->join('activity' , 'activity.activity_id' , '=' ,'activity_assign.activity_id')
            ->join('login' , 'login.acc_id' , '=' , 'activity_assign.acc_id' )
            ->select('firstname', 'lastname', 'middle', 'title', 'activity.points' , 'activity_assign.status' , 'activity_assign.grade' , 'activity_assign.assign_id' , 'activity_assign.activity_id')->get();
            
            
            $responselist = [];
            foreach($responses as $item){
                $responselist[] = [
                    "name" => $item->lastname . ", " . $item->firstname . " " . $item->title ,
                    "status" => $item->status,
                    "grade" => $item->grade,
                    "assign_id" => $item->assign_id,
                    "activity_id" => $item->activity_id,
                    "points" => $item->points
                    
                ];
 
            }
            
            
            
            return $responselist;
        }
        
    }
    
    
      public function deleteclassworkcomment($type = null , $id = null ){
        if($type == "activitycomment"){
            $comment = Activitycomments::find($id);
            if ($comment  != null){
                $comment->delete();
            }
        }else if($type == "announcementcomment"){
            $comment = Announcementcomments->find($id);
            if($comment != null){
                $comment->delete();
            }
        }else{
            
        }
        
         
    }
    
    
        
// public function uploadstudent(Request $request)
// {
//     if ($request->hasFile('uploadedfile') && $request->file('uploadedfile')->isValid()) {
//         $file = $request->file('uploadedfile');
//         $path = Storage::disk('public')->putFile('uploads/student', $file);
//         $url = Storage::url($path);
//         $fileData = [
//             'name' => $file->getClientOriginalName(),
//             'type' => $file->getClientMimeType(),
//             'size' => $file->getSize()
//         ];
       
//         return response()->json([
//             'message' => 'File uploaded successfully.',
//             'url' => $url,
//             'path' => $path,
//             'data' => $fileData
//         ]);
//     } else {
//         return response()->json([
//             'message' => 'Please upload a file.'
//         ], 400);
//     }
// }
    
    
//     public function handIn(Request $request){
//         $activityassign = Activity_assign::find($request->input('assign_id'));
     
        
//         if($activityassign != null){
//             $activity = Activities::find( $activityassign->activity_id);
            
//             if($activity != null){
//                 $activityassign->uploadedfile = $request->input('uploadedfile');
                
//                 $date = Carbon::parse($activity->date_due);
//                 $now = Carbon::now();
//                 if($activity->activity_type!= "Material" &&  $now->isAfter($date)){
//                     if($activity->allow_late == 1){
//                         $activityassign->status = "handed in late";
//                         $activityassign->update();
//                     }
                    
//                 }else{
//                         $activityassign->status = "done";
//                         $activityassign->update();
//                 }
//             }
      
//         }
        
//         return $activityassign;
//     }



public function getFile(Request $request)
{
    $activityAssign = Activity_assign::find($request->input('assign_id'));
    
    if ($activityAssign != null) {
        $filePath = $activityAssign->uploadedfile;
        
        // Check if the file path is not null and exists
        if (!empty($filePath) && Storage::exists($filePath)) {
            // Get the file contents and return as a response with the appropriate headers
            $fileContents = Storage::get($filePath);
            $fileMimeType = Storage::mimeType($filePath);
            $fileName = basename($filePath);
            $headers = [
                'Content-Type' => $fileMimeType,
                'Content-Disposition' => 'inline; filename="'.$fileName.'"'
            ];
            return response()->make($fileContents, 200, $headers);
        } else {
            // Return an error message if the file path is invalid
            return response()->json([
                'message' => 'File not found.'
            ], 404);
        }
    } else {
        // Return an error message if the activity_assign record is not found
        return response()->json([
            'message' => 'Activity assign not found.'
        ], 404);
    }
}


public function uploadstudent(Request $request)
{
    if ($request->hasFile('uploadedfile') && $request->file('uploadedfile')->isValid()) {
        $file = $request->file('uploadedfile');
        $path = $file->store('uploads/student', 'public');
        $url = Storage::url($path);
        $fileData = [
            'name' => $file->getClientOriginalName(),
            'type' => $file->getClientMimeType(),
            'size' => $file->getSize()
        ];
       
        return response()->json([
            'message' => 'File uploaded successfully.',
            'url' => $url,
            'path' => $path,
            'data' => $fileData
        ]);
    } else {
        // If no file is uploaded, set uploadedfile to null
        return response()->json([
            'message' => 'No file uploaded.',
            'url' => null,
            'path' => null,
            'data' => null
        ]);
    }
}

public function handIn(Request $request){
    $activityassign = Activity_assign::find($request->input('assign_id'));

    if($activityassign != null){
        $activity = Activities::find($activityassign->activity_id);

        if($activity != null){
            $activityassign->uploadedfile = $request->input('uploadedfile');

            // Update uploadedfile with the URL value if it exists
            if($request->has('url')) {
                $activityassign->uploadedfile = $request->input('url');
            } elseif (empty($request->input('url'))) { // Add this block to set uploadedfile to null if the url is empty
                $activityassign->uploadedfile = null;
            }

            $date = Carbon::parse($activity->date_due);
            $now = Carbon::now();

            if($activity->activity_type != "Material" && $now->isAfter($date)){
                if($activity->allow_late == 1){
                    $activityassign->status = "handed in late";
                    $activityassign->update();
                }
            } else {
                $activityassign->status = "done";
                $activityassign->update();
            }
        }
    }

    return $activityassign;
}

    
    
    public function unSubmit(Request $request){
        $activityassign = Activity_assign::find($request->input('assign_id'));
        
        
        if($activityassign!= null){
            $activityassign->status = "pending";
            $activityassign->save();
        
        }
        
          return $activityassign;
         
    }
    
    
    public function setGrade(Request $request){
        $actassign = Activity_assign::find($request->input('assign_id'));
        if($actassign!= null){
                $actassign->grade = $request->input('score');
                $actassign->save();
        }
        
        
    }


    
        
}



    

