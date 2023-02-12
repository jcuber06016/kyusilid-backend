<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Attendance;
use App\Models\Classinfo;
use Illuminate\Http\Request;

class AttendanceController extends Controller
{
    public function attendancein(Request $request)
    {
        $attendance = new Attendance;

        $classinfo = Classinfo::find($request->classes_id);
        $start_time = Carbon::parse($classinfo->sched_from);
        $duration = $classinfo->duration->diffInMinutes('00:00:00') + $classinfo->grace_period->diffInMinutes('00:00:00');
        $end_time = $start_time->addMinutes($duration);

        $attendance->start_time = $start_time;
        $attendance->end_time = $end_time;
        $attendance->date_created = Carbon::now();

        $attendance->save();


        return response()->json(['message' => 'Attendance created successfully']);

    }
    //
}
