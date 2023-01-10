<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\announcement;

class announcements extends Controller
{
    function list(){
        return announcement::all();
    }
}
