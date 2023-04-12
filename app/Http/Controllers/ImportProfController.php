<?php
namespace App\Http\Controllers;

use App\Models\DeptModel;
use App\Models\Professor;
use App\Models\UserModel;
use Illuminate\Http\Request;



class ImportProfController extends Controller
{
    
    


    public function importprof(Request $request){
        
        $addedproff = [];
        $updatedproff= [];
        

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
            if (array_key_exists('Email', $row)) {
                // Find the student with the given email (if it exists)
                $proff = UserModel::where('acc_email', $row['Email'])->first();

        
                if($proff != null) {
                    $proff->acc_email = $row['Email'];
                    $proff->acc_username = $row['Email'];
                    $proff->firstname = $row['Firstname'];
                    $proff->lastname =$row['Lastname'];
                    $proff->middle = $row['Middlename'];
                    $proff->acc_password = password_hash($row['FacultyID'], PASSWORD_DEFAULT); // Hash the password
                    $proff->update();
                 
                 
                    
                        $department = DeptModel::where('dep_name', $row['Department'])->first();
                        if ($department != null) {
                            $depid = $department->dep_id;
                        } else {
                            $depid = null; // or whatever default value you want
                        }
                        
                         $prof2 = Professor::where('faculty_id', $row['FacultyID'])->first();
                     
                            if($prof2 !=null){
                            $prof2->faculty_id = $row['FacultyID'];
                            $prof2->dep_id = $depid;
                            $prof2->update();
                            $importSuccess = true;
                            }
                            
                             $updatedproff[]=[
                            "id" => $proff->acc_id,
                            "proffname" => $row['Lastname'] . ", " . $row['Firstname'] . " " .$row['Middlename'] ,
                            "faculty_id" => $prof2->faculty_id
                         
                        ];
                         
                            
                
                } 
                else {
                    // If the professor doesn't exist, create a new record
                    $user = new UserModel([
                        'acc_email' => $row['Email'],
                        'acc_username' => $row['Email'],    
                        'usertype' => "prof",
                        'firstname' => $row['Firstname'],
                        'middle' => $row['Middlename'],
                        'lastname' => $row['Lastname'],
                        'acc_password' => password_hash($row['FacultyID'], PASSWORD_DEFAULT), // Hash the password
                        // ...
                    ]);
                    $user->save();
                    
                    if(!empty($row['Department'])){
                    
                   // Check if the department exists, otherwise create a new record
                    $department = DeptModel::where('dep_name', $row['Department'])->first();
                    if ($department !=null) {
                        $depid = $department->dep_id;
                    } else {
                        
                        $newDepartment = new DeptModel();
                        $newDepartment->dep_name = $row['Department'];
                        $newDepartment->save();
                        $depid = $newDepartment->dep_id;
                    }

                    $proff = new Professor();
                    $proff->faculty_id= $row["FacultyID"];
                    $proff->acc_id = $user->acc_id;
                    $proff->dep_id = $depid; 
                    $proff->save();
                    $importSuccess = true;
                }
                else{
                    return "Empty";
                }
                                        
                }
            }
        
        }
        
        $updatelist = [
            "addedproff" => $addedproff,
            "updatedproff" => $updatedproff
            ];
            return response()->json(['success' => $importSuccess , "updatelist" => $updatelist]);

    }
   

   

    // Insert the rows into the database using your preferred database library
    
    

    // Return a response indicating success
   
}



