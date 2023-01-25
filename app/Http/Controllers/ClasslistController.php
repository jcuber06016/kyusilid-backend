<?php

namespace App\Http\Controllers;

use App\Models\Classinfo;
use Illuminate\Http\Request;
use App\Models\Classlist;
use Spatie\FlareClient\Api;
use Illuminate\Support\Facades\DB;

class ClasslistController extends Controller
{
    public function getclasslist($id = null){
    return DB::table('classlist')
    ->join('classinfo', 'classinfo.classes_id' , "=" , 'classlist.classes_id')
    ->join('subject' , 'subject.sub_id' ,"=" ,"classinfo.sub_id" )
    ->join('professor' , 'professor.pf_id', "=", "classinfo.pf_id")
    ->join('days', 'classinfo.day_id' ,'=', 'days.day_id')
    ->join('section' , 'section.sec_id' , "=", 'classinfo.sec_id')
    ->select('days.day_label' , 'professor.pf_firstname' , 'professor.pf_lastname' , 'subject.sub_name' ,'classinfo.sched_from' , 'classinfo.sched_to' , 'classlist.year_id', 'section.sec_name' ,'subject.sub_code')
    ->where('classlist.stud_id', $id)
    ->get();
}
}


     
