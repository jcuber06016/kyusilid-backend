<?php

namespace App\Http\Controllers;

use App\Models\Classinfo;
use App\Models\Classlist;
use App\Models\Days;
use App\Models\Department;
use App\Models\DeptModel;
use App\Models\Section;
use App\Models\Student;
use App\Models\Subject;
use App\Models\UserModel;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;


class ImportClsController extends Controller
{
    
    public function importclass(Request $request){

        $addedclasses = [];
        $editedclasses = [];
        $addedmodulesources = [];
        $addedsubj = [];
        $updatedsubj = [];
      

        // Get the file from the request
   $file = $request->file('file');

   // Load the Excel file using PhpSpreadsheet
   $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
   $spreadsheet = $reader->load($file);
   $worksheet = $spreadsheet->getActiveSheet();

   // Get the column names from the first row of the worksheet
   $columnNames = [];
   $rowIterator = $worksheet->getRowIterator();
   $firstRow = $rowIterator->current();
   $cellIterator = $firstRow->getCellIterator();
   $cellIterator->setIterateOnlyExistingCells(false);
   foreach ($cellIterator as $cell) {
       $columnNames[] = $cell->getValue();
   }



    // Loop through each row of the worksheet
    $rows = [];
       foreach ($rowIterator as $row) {
           // Skip the first row (heading row)
           if ($row->getRowIndex() === 1) {
               continue;
           }
   
           // Get the cell values for this row
           $cellIterator = $row->getCellIterator();
           $cellIterator->setIterateOnlyExistingCells(false);
           $cells = [];
           foreach ($cellIterator as $cell) {
               $cells[] = $cell->getValue();
           }
   
           // Map the row data to the appropriate database columns using the column names
           $rowData = [];
           foreach ($columnNames as $index => $columnName) {
               $rowData[$columnName] = $cells[$index] ?? null;
           }
           

   
           // Add the row data to the rows array
           $rows[] = $rowData;
       }
       

       
       foreach ($rows as $row) {
        $subtempyr = null;
       $subjectid_temp = null;
        $tempsub_name = $row['SubjectName'];
        
        // Check if the Subject Code key exists in the row data
        if (!empty($row['SubjectCode'])) {
            $subtemp = Subject::where("sub_code", $row['SubjectCode'])->first();
        
            // set the units based on sub_code
            if (strpos($row['SubjectCode'], "PE") !== false) {
                $units = 2;
            } else {
                $units = 3;
            }
        
                $department = DeptModel::where('dep_name', $row['Department'])->first();
                    if ($department) {
                        $subdep = $department->dep_id;
                    } else {
                        $subdep = null; // or whatever default value you want
                    }
            
                  
                        if (!$subtemp) {
                            // Create a new subject if it doesn't exist
                            $newsub = new Subject();
                            $newsub->sub_code = $row['SubjectCode'];
                            $newsub->sub_name = $tempsub_name;
                            $newsub->dep_id = $subdep;
                            $newsub->units = $units;
                            $newsub->save();
                            $addedsubj[] =[
                                "id" => $newsub->sub_id,
                                "sub_name" => $newsub->sub_name,
                                "sub_code" => $newsub->sub_code
                                ];
                            $subtempyr= $newsub->yearlvl;
                            $subjectid_temp = $newsub->sub_id;
                        } else {
                            // Update the existing subject if it exists
                            $subtemp->sub_name = $tempsub_name;
                            $subtemp->dep_id = $subdep;
                            $subtemp->units = $units;
                            $subtemp->save();
                              $updatedsubj[] =[
                                "id" => $subtemp->sub_id,
                                "sub_name" => $subtemp->sub_name,
                                "sub_code" => $subtemp->sub_code
                                ];
                            $subtempyr= $subtemp->yearlvl;
                            $subjectid_temp = $subtemp->sub_id;
                        }
                    
                    

                    //Creating class
                    
                            $sectiontemp = DB::table('section')
                                ->where('sec_name', $row['Section'])
                                ->where('dep_id', 1)
                                ->select('sec_id')
                                ->first();


                                $classtemp = DB::table('classinfo')->where('sec_id', $sectiontemp->sec_id)->where('sub_id', $subjectid_temp)->first();
          
                       
                                 $classInfo = DB::table('classinfo')
                                ->where('sub_id', $subjectid_temp)
                                ->whereNotNull('class_comment')
                                ->first();
                                
                                $modulesourcetemp = null;
                                
        
                                if ($classInfo == null) {
                                    // If the record doesn't exist, create a new one with the sub_id and a default class_comment
                                  
                                    
                                    $newmodulesource = new Classinfo();
                                    $newmodulesource->sub_id = $subjectid_temp;
                                    $newmodulesource->class_comment = "class module for {$tempsub_name}";
                                    $newmodulesource->save();
                                    $addedmodulesources[] = [
                                 
                                        "classes_id" => $newmodulesource->classes_id,
                                        "subject" => $row['SubjectName']
                                    ];

                                    $modulesourcetemp = $newmodulesource->classes_id;
                                    
                                    } else {
                                         $modulesourcetemp = $classInfo->classes_id;
                                       //Do nothing
                                    }
     

                             // $daytemp = ['Monday'=>1, 'Tuesday'=>2, 'Wednesday'=>3, 'Thursday'=>4, 'Friday'=>5, 'Saturday'=>6];
        
  

                                    if ($classtemp != null) {
                                        $classtemp2 = Classinfo::find($classtemp->classes_id);
                                       if($classtemp2->sessionname1 != $row['Type']){
                                   
                                        if($row['Type'] == "Lecture"){
                                            $classtemp2->sessionname2 = "Laboratory";
                                        }
                                        else{
                                            $classtemp2->sessionname2 = "Lecture";
                                        }
                                            }

                                        if ($row['Type'] == "Lecture") {
                                        $classtemp2->sched_from = Carbon::createFromTimeString('00:00:00')->addSeconds($row['Start'] * 86400) ->format('H:i:s');
                                        $classtemp2->sched_to =   Carbon::createFromTimeString('00:00:00')->addSeconds($row['End'] * 86400) ->format('H:i:s');
                                        } else {
                                        $classtemp2->sched_from2 = Carbon::createFromTimeString('00:00:00')->addSeconds($row['Start'] * 86400) ->format('H:i:s');
                                        $classtemp2->sched_to2 =   Carbon::createFromTimeString('00:00:00')->addSeconds($row['End'] * 86400) ->format('H:i:s');
                                       
                                        }
                                        $classtemp2->moduleSource = $modulesourcetemp;
                                        $classtemp2->yearlvl = $subtempyr;
                                        
                                        $classtemp2->update();
                                        
                                        
                                        $editedclasses[]=[ 
                                            "classes_id" => $classtemp2->classes_id,
                                            "yearandsection" => $classtemp2->yearlvl . $row["Section"],
                                            "subject" => $row["SubjectName"]
                                            ];
                                    
         

                                        } else {
                                            $newclass = new Classinfo();   
                                            $newclass->sec_id = $sectiontemp->sec_id;
                                            $newclass->sub_id = $subjectid_temp;
                                            $newclass->yearlvl = 4;
                                            $newclass->sessionname1= $row['Type'];
                                            $newclass->sched_from = Carbon::createFromTimeString('00:00:00')->addSeconds($row['Start'] * 86400) ->format('H:i:s');
                                            $newclass->sched_to = Carbon::createFromTimeString('00:00:00')->addSeconds($row['End'] * 86400) ->format('H:i:s');
                                            $newclass->moduleSource = $modulesourcetemp; 
                                            $newclass->yearlvl = $subtempyr;
                                        
                            
                                       
                                          
                                //Exploding the name of professor from excel
                                
                                if(!empty($row['Professor'])){
                                    
                                 
                                // Check the professors Name 
                                $namearray1 = explode(',', $row['Professor']);
                
                                // find in login where first name = $namearray[1] and lastname = $namearray[0] and acc type == prof
                                $professorname = DB::table('login')
                                            ->where('firstname', trim($namearray1[1]))
                                            ->where('lastname', trim($namearray1[0]))
                                            ->first();
                
                                            if ($professorname !=null) {
                                                $profget = $professorname->acc_id;
                                            } else {
                                                $profget = null; // or whatever default value you want
                                            }
                                }
                                else{
                                    info('proff error');
                                }
                
                
                            if(!empty($row['Day'])){
                            $day = Days::where('day_label',$row['Day'])->first();
                                if ($day !== null){
                                    $days = $day->day_id; 
                
                                }
                                else{
                                    $days = null;
                                }
                            }
                            $newclass->day_id=$days;
                            $newclass->dep_id = $subdep;     
                            $newclass->isarchived = 0;
                            $newclass->acc_id = $profget;
                            $newclass->save();
                             $addedclasses[]=[ 
                                            "classes_id" => $newclass->classes_id,
                                            "yearandsection" => $newclass->yearlvl . $row["Section"],
                                            "subject" => $row["SubjectName"]
                                            ];
                        }    
                    }       
                        

        



        //Assign student / prof

            // Get acc_id based on student_no
       
            if(!empty($row['Studentno'])){
            $studentNo = $row['Studentno'];
            $account = Student::where('stud_no', $studentNo)->first();
            $acc_id = $account->acc_id;
            
            
           
             $sectiontemp2 = DB::table('section')
                                ->where('sec_name', $row['Section'])
                                ->where('dep_id', 1)
                                ->select('sec_id')
                                ->first();
                                
    
            $subjectid_temp2 = Subject::where('sub_name',$row['SubjectName'])->first();
            if ($subjectid_temp2 !== null){
            $sub_id_get = $subjectid_temp2->sub_id; 
                
            }
            else{
                $sub_id_get = null;
                }
            

            if($sectiontemp2 !==null){
               
            // Get classes_id based on classinfo table
            $section_id = $sectiontemp2->sec_id;
            $sub_id = $sub_id_get;
                
            $class = Classinfo::where('sec_id', $section_id)
                            ->where('sub_id', $sub_id)
                            ->first();

            if ($class != null) {
                $classes_id = $class->classes_id;

                // Find in classlist where acc_id and classes_id
                $classlist = Classlist::where('acc_id', $acc_id)
                                    ->where('classes_id', $classes_id)
                                    ->first();

                // If found, update the record. Otherwise, create a new record.
                if ($classlist != null) {
                    $classlist->update([
                        'acc_id' => $acc_id,
                        'classes_id' => $classes_id
                    ]);
                } else {
                    $newClasslist = new Classlist();
                    $newClasslist->acc_id = $acc_id;
                    $newClasslist->classes_id = $classes_id;
                    $newClasslist->save();
                    info('Successssssss');
                }
            }
        }
        else{
            return "Error assigning student";
        }

            //assigning prof

            // create an array from "Professor name" split by comma
            $namearray = explode(',', $row['Professor']);

            // find in login where first name = $namearray[1] and lastname = $namearray[0] and acc type == prof
            $professor = DB::table('login')
                        ->where('firstname', trim($namearray[1]))
                        ->where('lastname', trim($namearray[0]))
                        ->first();

            // if logindata exists, insert in classlist logindata->acc_id, classinfo = classlist->classes_id
            if ($professor != null) {

                $checkassign = DB::table('classlist')->where('acc_id', $professor->acc_id)->where('classes_id', $classes_id)->first();

                // If found, update the record. Otherwise, create a new record.
                if ($checkassign != null) {
                    Classlist::where('acc_id', $professor->acc_id)->where('classes_id', $classes_id)
                            ->update([
                                'acc_id' => $professor->acc_id,
                                'classes_id' => $classes_id
                            ]);
                            $importSuccess = true;
                           
                } else {
                    $classlist = new Classlist();
                    $classlist->acc_id = $professor->acc_id;
                    $classlist->classes_id = $classes_id;
                    $classlist->save();
                    $importSuccess = true;

                 
                }
                
        }
            }
            else{
                    info('student no error');
            }
       

     
    }
    
    $updatelist =[
        "addedsubj"=> $addedsubj,
        "updatedsubj" => $updatedsubj,
        "addedmodulesource" => $addedmodulesources,
        "updatedclasses" => $editedclasses,
        "addedclasses" => $addedclasses
        
    
    ];
       return response()->json(['success' => $importSuccess , 'updatelist'=> $updatelist]);

   }
  
}
