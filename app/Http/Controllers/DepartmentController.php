<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DepartmentController extends Controller
{
    function getdepartment($id= null){
        $depadminlist = DB::table('department')->join('admin' , 'admin.dep_id', "=" , 'department.dep_id')
        ->where('admin.acc_id' , $id)
        ->get();


       


        return $depadminlist;




    }

  

}
