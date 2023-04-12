<?php

namespace App\Http\Controllers;

use Carbon\Carbon;

class TimeController extends Controller
{
    public function getCurrentTime()
    {
        $currentDateTime = Carbon::now();
        $currentDay = $currentDateTime->format('l'); // format() method returns the current day as a string in format "Monday", "Tuesday", etc.
        $currentTime = $currentDateTime->format('H:i:s'); // format() method returns the current time as a string in format "HH:mm:ss"
        return response()->json([
            'currentDay' => $currentDay,
            'currentTime' => $currentTime,
        ]);
    }
}