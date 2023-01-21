<?php
namespace App\Http\Controllers;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\UserModel;

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
        $user= UserModel::where ('acc_username', $request->acc_username)->first();
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
                            'usertype' => 'stud'
                        ]);
                    } elseif($user->usertype === 'prof') {
                        return response()->json([
                            'status' => 'success',
                            'user' => $user,
                            'usertype' => 'prof'
                        ]);
                    } else {
                        return response()->json(['status' => 'error sa else']);
                    }
    
        }
       
        


        // if (Auth::guard('login')->attempt($credentials)) {
        //     // Authentication passed...
        //     $user = Auth::guard('login')->user();

        //     if ($user->usertype === 'admin') {
        //         return response()->json([
        //             'status' => 'success',
        //             'user' => $user,
        //             'usertype' => 'admin'
        //         ]);
        //     } elseif ($user->usertype === 'stud') {
        //         return response()->json([
        //             'status' => 'success',
        //             'user' => $user,
        //             'usertype' => 'stud'
        //         ]);
        //     } elseif($user->usertype === 'prof') {
        //         return response()->json([
        //             'status' => 'success',
        //             'user' => $user,
        //             'usertype' => 'prof'
        //         ]);
        //     } else {
        //         return response()->json(['status' => 'error sa else']);
        //     }
        // }
        // return response()->json(['status' => 'error sa return']);
        
    }
    //
}
