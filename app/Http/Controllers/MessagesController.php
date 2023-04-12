<?php

namespace App\Http\Controllers;

use App\Models\Messages;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class MessagesController extends Controller
{
    public function getmessages($id = null) { //id = classes_id
        $temp = DB::table('messages')
        ->join('login' , 'messages.acc_id' , "=" , 'login.acc_id')
        ->select('firstname' , 'lastname' , 'login.acc_id', 'message_content' , 'created_at' ,'message_id' , 'isdeleted')
        ->where('classes_id' , $id) ->get();

        return $temp;
    }

    public function createmessage(Request $request){
        $newmessage= new Messages();
        $newmessage->classes_id = $request->input('classes_id');
        $newmessage->acc_id = $request->input('acc_id');
        $newmessage->message_content = $request->input('message_content');
        $newmessage->created_at = Carbon::now();
        $newmessage->save(); 

        $temp = DB::table('messages')
        ->join('login' , 'messages.acc_id' , "=" , 'login.acc_id')
        ->select('firstname' , 'lastname' , 'login.acc_id', 'message_content' , 'created_at' ,'message_id' ,'isdeleted')
        ->where('message_id' , $newmessage->message_id)->first();

        return $temp;
      



    }

    public function deletemessage($id){ //id == message id
        $deletemessage = Messages::find( $id);
        $deletemessage->isdeleted = 1;
        $deletemessage->update();
    }
}
