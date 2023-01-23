<?php

namespace App\Http\Controllers;

use App\Models\ClassListModel;
use Illuminate\Http\Request;

class ClasslistController extends Controller
{

    public function index()
     {
        $Classlist = ClassListModel::all();
         return response()->json($Classlist);
     }
    //
}
