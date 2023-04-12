<?php
namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\User;
use App\Models\Adminlog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Models\UserModel;
use App\Models\DeptModel;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;


class UserController extends Controller
{
    public function register(Request $request)
    {
        $user= new UserModel;
        $user->acc_username=$request->input('acc_username');
        $user->usertype=$request->input('usertype');
        $user->acc_email=$request->input('acc_email');
        $user->acc_password=Hash::make($request->input('acc_password'));
        $user->save();
        return $user;
    }
    
    public function login(Request $request)
{
    $credentials = $request->only('acc_username', 'acc_password');

    $user = UserModel::where('acc_username', $request->acc_username)->where("active" , 1)->with('professor', 'student')->first();

    if (!$user || !Hash::check($request->acc_password, $user->acc_password)) {
        return ["error" => "Email or Password is incorrect"];
    } else {
        // authenticate the user
        Auth::login($user);

        if ($user->usertype === 'admin') {
            // create timestamp
            $admintime = new Adminlog();
            $admintime->acc_id = $user->acc_id;
            $temp =  Carbon::now();
                        
            $admintime->created_at = $temp->format('Y-m-d');
            $admintime->created_at_time = $temp->format('H:i:s');
            $admintime->save();

            return response()->json([
                'status' => 'success',
                'user' => $user,
                'usertype' => 'admin',
                
            ]);
        } elseif ($user->usertype === 'stud') {
            return response()->json([
                'status' => 'success',
                'user' => $user,
                'usertype' => 'stud',
                'temp' => $user->student->stud_no,
                
                'newuser' => $user->first_login
            ]);
        } elseif ($user->usertype === 'prof') {
            $temptemp = DeptModel::select('dep_name')
                ->where('dep_id', '=', $user->professor->dep_id)
                ->first();
            return response()->json([
                'status' => 'success',
                'user' => $user,
                'usertype' => 'prof',
                'temp' => $temptemp->dep_name,
                
                'newuser' => $user->first_login
        
            ]);
        } else {
            return response()->json(['status' => 'error sa else']);
        }
    }
}
      public function resetPassword(Request $request)
    {
        
        $user=UserModel::find($request->input('acc_id'));
        if($user !==null){
            $user->acc_password = Hash::make($request->input('acc_password'));
            $user->first_login = 0;
            $user->update();
        }
        else{
            return " may Error";
        }
       

        return response()->json(['success' => 'Password has been reset.']);
    }
    
    
    public function changepass(Request $request){
        $user = UserModel::find($request->input('acc_id'));
        if($user !== null){
            $user->acc_password = Hash::make($request->input('acc_password'));
            $user->update(); 
        }
    }
    
public function updateProfilePic(Request $request, $acc_id)
{
     $user = UserModel::find($acc_id);
    // Find the user model with the given acc_id
    if (!$user) {
        return response()->json(['message' => 'User not found'], 404);
    }
            
         if ($request->hasFile('profile_pic') && $request->file('profile_pic')->isValid())
            {
            $file = $request->file('profile_pic');
            $path = Storage::putFile('profile_pic', $file);
            $url = Storage::url($path);
            $user->profile_pic = $url;
            $user->save();
            return response()->json([
                'message' => 'Profile picture updated successfully.',
                'url'=>$url,
                'path'=>$path
            ]);
        } else {
            
            return response()->json([
                'message' => 'Please upload a profile picture.'
            ], 400); 
        }
    }
    
    
     
    public function setuseractivate(Request $request){
        $login = UserModel::find($request->input("acc_id"));
        
        
        if($login != null){
            $login->active = $request->input("active");
            $login->save();
         
        }
        
        ///ulitin ung accountlist
        
        
          $studentlist= DB::table('login')
        ->join('student' , 'student.acc_id' ,'=' , 'login.acc_id')
        ->where('student.dep_id', $request->input('dep_id'))->get();
        
        $studentlist2 = [];
        foreach($studentlist as $studitem){
            $studentlist2[] = [
                'name' => $studitem->firstname . ' ' . $studitem->middle. ' ' . $studitem->lastname,
                'studnum' => $studitem->stud_no ,
                'status' => $studitem->status,
                'active' => $studitem->active,
                'acc_id' => $studitem->acc_id
            ];
        }


        $proflist = DB::table('login')
        ->join('professor' , "professor.acc_id" , '=', 'login.acc_id')
        ->where('professor.dep_id' , $request->input('dep_id'))->get();

        $proflist2= [];

        foreach($proflist as $profitem){
            $proflist2[]= [
                'acc_id' => $profitem->acc_id,
                'name' =>$profitem->firstname . ' ' . $profitem->middle. ' ' . $profitem->lastname,
                'faculty_id' => $profitem->faculty_id,
                'active'=> $profitem->active
                
            ];
        }

        return [
            'studentlist' => $studentlist2,
            'proflist' =>$proflist2
        ];
        
       
        
      
        
        
    }

}
