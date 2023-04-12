<?php

namespace App\Http\Controllers;


use Illuminate\Support\Facades\DB;
use Symfony\Component\CssSelector\Node\FunctionNode;
use Illuminate\Http\Request;
use App\Models\Classinfo;
use App\Models\Subject;

class ClasslistController extends Controller
{
    
       public function getclasslist($id = null){

       
            return DB::table('classlist')
            ->join('classinfo', 'classinfo.classes_id' , "=" , 'classlist.classes_id')
            ->join('subject' , 'subject.sub_id' ,"=" ,"classinfo.sub_id" )
            ->join('professor' , 'professor.acc_id', "=", "classinfo.acc_id")
            ->join('days', 'classinfo.day_id' ,'=', 'days.day_id')
            ->join('section' , 'section.sec_id' , "=", 'classinfo.sec_id')
            ->join('login' , 'login.acc_id' , "=", 'classinfo.acc_id' )
            ->select('days.day_label' , 
                        'login.firstname' , 
                        'login.lastname',
                        'login.middle',
                        'login.suffix', 
                        'login.title',
                        'subject.sub_name' ,
                        'classinfo.sessionname1',
                        'classinfo.sessionname2',
                        DB::raw("DATE_FORMAT(classinfo.sched_from, '%h:%i %p') as sched_from"),
                        DB::raw("DATE_FORMAT(classinfo.sched_to, '%h:%i %p') as sched_to"),
                        'classinfo.sched_from2',
                        'classinfo.sched_to2',
                        'classinfo.yearlvl', 
                        'section.sec_name' ,
                        'subject.sub_code' , 
                        'classinfo.classes_id' , 
                        'login.acc_id' , 
                        'classinfo.classbanner',
                        'isarchived',
                        'login.usertype',
                        'moduleSource',
                        'class_link'
                       
                        )
            ->where('classlist.acc_id', $id)
            ->where('classinfo.isarchived' , 0)
            ->get();
   
            }

    
  

   
    public function checkusertype($id= null){
        $temp = DB::table('login')->where('login.acc_id', $id)->first();
        return $temp;    
    }


    public function getclasslist_archived($id = null){
        return DB::table('classlist')
            ->join('classinfo', 'classinfo.classes_id' , "=" , 'classlist.classes_id')
            ->join('subject' , 'subject.sub_id' ,"=" ,"classinfo.sub_id" )
            ->join('professor' , 'professor.acc_id', "=", "classinfo.acc_id")
            ->join('days', 'classinfo.day_id' ,'=', 'days.day_id')
            ->join('section' , 'section.sec_id' , "=", 'classinfo.sec_id')
            ->join('login' , 'login.acc_id' , "=", 'classinfo.acc_id' )
            ->select('days.day_label' , 
                        'login.firstname' , 
                        'login.lastname',
                        'login.middle',
                        'login.suffix', 
                        'login.title',
                        'subject.sub_name' ,
                        'classinfo.sessionname1',
                        'classinfo.sessionname2',
                        'classinfo.sched_from' , 
                        'classinfo.sched_to' , 
                        'classinfo.sched_from2',
                        'classinfo.sched_to2',
                        'classinfo.yearlvl', 
                        'section.sec_name' ,
                        'subject.sub_code' , 
                        'classinfo.classes_id' , 
                        'login.acc_id' , 
                        'classinfo.classbanner',
                        'isarchived',
                        'login.usertype')
            ->where('classlist.acc_id', $id)
            ->where('classinfo.isarchived' , 1)
            ->get();
    }
    
   public function createclass(Request $request){
        $temp1 = $request->input('sessionname1');

        if($temp1 == null){
            $temp1 = 'Lecture';
        }
        $temp2 = $request->input('sessionname2');
        if($temp2 == null){
            $temp2 = '';
        }

        $newclass = new Classinfo();
        $newclass->dep_id = $request->input('dep_id');
        
        $newclass->sec_id = $request->input('sec_id');
        $newclass->sub_id = $request->input('sub_id');
        $newclass->day_id= $request->input('day_id');
        $newclass->sched_from= $request->input('sched_from');
        $newclass->sched_to= $request->input('sched_to');
        $newclass->sessionname1= $temp1;
        $newclass->sessionname2= $temp2;
        $newclass->sched_from2= $request->input('sched_from2');
        $newclass->sched_to2= $request->input('sched_to2');
        
     
        
        //check if source module exists
        $sourcemoduletemp = DB::table('classinfo')
        ->where('sec_id' , null)
        ->where('sub_id' , $request->input('sub_id'))
        ->select('classes_id' , 'sub_id' , 'sec_id')
        ->first();
        
         $getthesubjectname = Subject::find($request->input("sub_id"));

        
        if($sourcemoduletemp != null){  //if exists
            $newclass->moduleSource= $sourcemoduletemp->classes_id;
            $newclass->yearlvl = $getthesubjectname->yearlvl;
        }else{
   
            $newsource = new Classinfo();
            $newsource->class_comment = "source container for " . $getthesubjectname ->sub_name;
            $newsource->sub_id = $request->input('sub_id');
            $newsource->sessionname1 = " ";
            
            $newclass->moduleSource = $newsource-> classes_id;
            $newclass->yearlvl = $getthesubjectname->yearlvl;
            
            $newsource -> save();
            
     
      
        }
 
        $newclass->save();
        return $newclass;
        

    }

     public function getcreateclassvalue($id= null){
        $subjectlist = DB::table('subject')
        ->where('dep_id' , $id)
        ->get();

       $sectionlist = DB::table('section')
       ->where('dep_id' , $id)->get();


        $temp = [
            'subjectlist' => $subjectlist,
            'sectionlist' =>$sectionlist
        ];

        return $temp;
        
    }
    
    
    
    // public function setclassbanner(Request $request){
    //     $classinfo = Classinfo::find($reques->input('classes_id'));
        
    //     if($classinfo != undefined){
    //         $classinfo->classbanner = $request->input('classbanner');
    //         $classinfo->save();
    //     }
    // }
    
    
    
    public function classlink(Request $request){
        $classinfo = Classinfo::find($request->input('classinfo'));
        if($classinfo != undefined){
            $classinfo->classlink = $request->input('classlink');
            $classinfo->save();
        }
        
    }
    
    
    
    // public function archiveclasses(Request $request){
        
    //     $archivedselection = $request->input('archivedclasses');
        
    //     foreach( $archivedselection as $archivedselectionitem){
    //         $selectedclass = classinfo::find($archivedselection['classes_id'])
    //          if($selectedclass != undefined){
      //              $selectedclass->isarchived = $archivedselection->isarchived;
    //               $selectedclass->save();
    //         }
    //     }
        
    // }
    
    
    // public function admingetclasses($id = null){
    //     $temp = DB::table('classinfo')
    //     ->where('dep_id' , $id)->get();
        
    //     return $temp;
    // }
    
    // public function profaccounts($id = null){
    //     $temp = DB::table('login')
    //     ->join('prof' , 'prof.acc_id' , "=" , "login.acc_id")
    //     ->where('prof.dep_id' , "$id")->get();
        
    //     $temp2 = DB::table('login')
    //     ->join('stud' , 'stud.acc_id' , "=" , "login.acc_id")
    //     ->where('stud.dep_id' , $id)->get();
        
    
    //     return {
    //         "proflist" : $temp,
    //         "studlist" : $temp2
    //     };
    
    
        
        
    // }
  
    
    
      public function admindashboard($id = null){
        $profcount = DB::table('login')
         ->join('professor' ,'professor.acc_id' , "=" , "login.acc_id")
        ->count();
        
        
        $studcount = DB::table('login')
        ->join('student' , 'student.acc_id' , "=", "login.acc_id")
        ->count();
        
        $archived = DB::table('classinfo')
        ->join('subject' , 'subject.sub_id' , '=', 'classinfo.sub_id')
        ->where('subject.dep_id' , $id)
        ->where('isarchived' , 1)->count();
        
        
      $classes = DB::table('classinfo')
        ->join('subject' , 'subject.sub_id' , '=', 'classinfo.sub_id')
    
        ->where('isarchived' , 0)->count();
        
        
      
        
        return [
            "studcount" => $studcount,
            "profcount" => $profcount,
            "archived" => $archived,
            "classes" => $classes
            
        ];
    }
    
    public function updateclasslist(Request $request){
      
    
    $classselection= json_decode($request->getContent());
    
    
    foreach($classselection as $classitem){
        $temp = Classinfo::find($classitem->itemselect->classes_id);
      if($temp != null){
          $temp->isarchived = $classitem->selected;
          $temp->update();
          
      }
       
    }
    
        
    }
    
    public function updateclassinfosettings( Request $request){
        $classinfo = Classinfo::find($request->input('classes_id'));
        
        
        if($classinfo !== null){
            $classinfo->class_link = $request->input('class_link');
            $classinfo->classbanner = $request->input('classbanner');
            $classinfo->update();
        }
    }
    
    
    
    
    
  
    
    
    

 
}


     
