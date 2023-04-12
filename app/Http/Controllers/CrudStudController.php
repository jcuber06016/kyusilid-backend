<?php

namespace App\Http\Controllers;
use App\Models\Student;
use App\Models\Professor;
use App\Models\UserModel;
use App\Models\Subject;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;

class CrudStudController extends Controller
{
    public function addstud(Request $request)
    {  
        $acc_email = UserModel::where('acc_email', $request->input('acc_email'))->first();
        if($acc_email){
            return response()->json([
        'message' => 'This user already exists.',
    ], 422);
        }else{
        
$acc_email=$request->input('acc_email');

        $login = new UserModel;
        $login -> acc_username=$request->input('acc_email');
        $login->acc_password = hash::make("pass");
        $login->acc_email = $request->input('acc_email');
        $login->usertype = "stud";
        $login->profile_pic = null;
        $login->firstname = $request->input('firstname');
        $login->lastname = $request->input('lastname');
        $login->middle = $request->input('middle');
        $login->suffix = $request->input('suffix');
        $login->title = " ";
        $login->save();
        
        $get = DB::table('login')
        ->where('acc_email', $acc_email)
        ->value('acc_id');

        $student= new Student;
        $student->stud_no=$request->input('stud_no');
        $student->stud_course=$request->input('stud_course');
        $student->acc_id=$get;
        $student->save();
        
        return back();
    }}
    
    public function studenttbl(Request $request)
    {
       $students = DB::table('login')
                ->where('usertype', '=', 'stud')
                ->get();
    }

    public function importstud(Request $request)
   {
    
   }

    public function addprof(Request $request)
    {
        $acc_email = UserModel::where('acc_email', $request->input('acc_email'))->first();
        if($acc_email){
            return response()->json([
        'message' => 'This user already exists.',
    ], 422);
        }else{
        $acc_email=$request->input('acc_email');

        $login = new UserModel;
        $login -> acc_username=$request->input('acc_email');
        $login->acc_password = hash::make("pass");
        $login->acc_email = $request->input('acc_email');
        $login->usertype = "stud";
        $login->profile_pic = null;
        $login->firstname = $request->input('firstname');
        $login->lastname = $request->input('lastname');
        $login->middle = $request->input('middle');
        $login->suffix = $request->input('suffix');
        $login->title = " ";
        $login->save();

        $get = DB::table('login')
        ->where('acc_email', $acc_email)
        ->value('acc_id');

        $prof = new Professor;
        $prof->acc_id = $get;
        $prof->dep_id = $request->input('dep_id');
        $prof->save();
        
        return back();
    }}
    public function addsubj(Request $request)
    {
        $sub_code = Subject::where('sub_code', $request->input('sub_code'))->first();
        if($sub_code){
            return back()->with('error', 'This subject already exists.');
        }else{

        $subj = new Subject;
        $subj->sub_code=$request->input('sub_code');
        $subj->sub_name=$request->input('sub_name');
        $subj->dep_id=$request->input('dep_id');
        $subj->sem_id=$request->input('sem_id');
        $subj->units=$request->input('units');
        $subj->save();
        
        return response()->json([
        'message' => 'This subject already exists.',
    ], 422);
       
    }}
}
