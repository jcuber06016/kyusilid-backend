<?php
namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\DeptModel;
use App\Models\Student;
use App\Models\UserModel;
use Illuminate\Http\Request;




class ImportController extends Controller
{


    public function import(Request $request){
        
        
        $addedstud =[];
        $updatedstud = [];

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
            // Check if the Student No. key exists in the row data
            if (array_key_exists('Studentno', $row)) {
                // Find the student with the given Student No. (if it exists)
                $student = Student::where('stud_no', $row['Studentno'])->first();
                $userstud = UserModel::where('acc_email', $row['Email'])->first();
        
                if($student != null) {
                   // If the student exists, update their information
                    $userstud->acc_email = $row['Email'];
                    $userstud->acc_username = $row['Username'];
                    $userstud->firstname = $row['Firstname'];
                    $userstud->lastname =$row['Lastname'];
                    $userstud->middle = $row['Middlename'];
                    $userstud->update();

            
        
                    $department = DeptModel::where('dep_name', $row['Course'])->first();
                        if ($department != null) {
                            $stud_course = $department->dep_id;
                        } else {
                            $new_department = new DeptModel([
                                'dep_name' => $row['Course'] // Set the department name to the value from the CSV row
                            ]);
                            $new_department->save();
                            $stud_course = $new_department->dep_id;
                        }            
                       
                        if($student !== null){
                        $student->stud_no = $row['Studentno'];
                        $student->stud_course = $stud_course;
                        $student->update();
                      
                        }
                    $importSuccess = true;
                    
                    $updatedstud[]= [
                        "studnum" =>$row['Studentno'], 
                        "studname" =>  $row['Lastname'] . ", " . $row['Firstname'] . " " . $row['Middlename']
                            
                        ];
                } 
                else {
                    // If the student doesn't exist, create a new record
                    $user = new UserModel([
                        'acc_email' => $row['Email'],
                        'acc_username' => $row['Username'],
                        'usertype' => "stud",
                        'firstname' => $row['Firstname'],
                        'middle' => $row['Middlename'],
                        'lastname' => $row['Lastname'],
                        'acc_password' => password_hash($row['Studentno'], PASSWORD_DEFAULT), // Hash the password
                        // ...
                    ]);
                    $user->save();
                    
                    if(!empty(['Course'])){
                    $department = DeptModel::where('dep_name', $row['Course'])->first();
                    if ($department !=null) {
                        $stud_course = $department->dep_id;
                    } else {
                             $new_department = new DeptModel([
                                'dep_name' => $row['Course'] // Set the department name to the value from the CSV row
                            ]);
                            $new_department->save();
                            $stud_course = $new_department->dep_id;
                    }
                    $student = new Student([
                        'acc_id' => $user->acc_id,
                        'stud_no' => $row['Studentno'],
                        'stud_course' => $stud_course,
                        // ...
                    ]);
                    $student['stud_course'] = $stud_course; 
                    $student->save();
                    
                      $addedstud[]= [
                        "studnum" =>$row['Studentno'], 
                        "studname" =>  $row['Lastname'] . ", " . $row['Firstname'] . " " . $row['Middlename']
                            
                        ];
                    
                    
                    $importSuccess = true;
                    }
                    else{
                        return "Empty";
                    }

                  
                    
                }
                 
            }
        
        }
        
        $updatelist = [
            "addedstud" => $addedstud,
            "updatedstud" => $updatedstud
            ];
        
        return response()->json(['success' => $importSuccess , "updatelist" => $updatelist ]);

    }
   

   

    // Insert the rows into the database using your preferred database library
    
    

    // Return a response indicating success
   
}



