<?php
namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\UserModel;
use App\Models\DeptModel;

class UserController extends Controller
{
    public function register(Request $request)
    {
        $user= new UserModel;
        $user->acc_username=$request->input('acc_username');
        $user->usertype=$request->input('usertype');
        $user->acc_email=$request->input('acc_email ');
        $user->acc_password=Hash::make($request->input('acc_password'));
        $user->save();
        return $user;
    }
    
    public function login(Request $request)
    {
        $credentials = new UserModel;
        $credentials = $request->only('acc_username', 'acc_password');


        // $user= UserModel::where ('acc_username', $request->acc_username)->first();


        // $user = UserModel::select('login.acc_username', 'login.usertype', 'student.stud_no')
        //         ->join('student', function ($join) {
        //             $join->on('login.acc_id', '=', 'student.acc_id')
        //                  ->where('login.acc_id', '=', 'student.acc_id');
        //         })
        //         ->where('login.acc_username', $request->acc_username)->with('student')->first();




        $user = UserModel::where('acc_username', $request->acc_username)->with('professor', 'student')->first();


        if(!$user || !Hash::check($request->acc_password,$user->acc_password))
        {
            return["error"=>"Email or Password is incorrect"];
        }
        else{
         
            if ($user->usertype === 'admin') {


                        return response()->json([
                            'status' => 'success',
                            'user' => $user,
                            'usertype' => 'admin'
                        ]);
                    } elseif ($user->usertype === 'stud') {
                        return response()->json([
                            'status' => 'success',
                            'user' => $user,
                            'usertype' => 'stud',
                            'temp' => $user->student->stud_no
                        ]);
                    } elseif($user->usertype === 'prof') {
                        $temptemp=DeptModel::select('dep_name')
                        ->where('dep_id', '=', $user->professor->dep_id)
                        ->first();
                        return response()->json([
                            'status' => 'success',
                            'user' => $user,
                            'usertype' => 'prof',
                            'temp' => $temptemp->dep_name
                          
                        ]);
                    } else {
                        return response()->json(['status' => 'error sa else']);
                    }
        }
    }
    //
}
