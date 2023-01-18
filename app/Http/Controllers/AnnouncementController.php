<?php
namespace App\Http\Controllers;
use App\Data;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Models\AnnouncementModel;

class AnnouncementController extends Controller
{
    public function store(Request $request) {
        $announcement = new AnnouncementModel;
        $announcement->an_title = $request->input('an_title');
        $announcement->an_content = $request->input('an_content');
        $announcement->save();

        return response()->json([
            'status'=>200,
            'message'=>'Announcement added',
        ]);
     }

     public function index()
     {
         $announcements = AnnouncementModel::all();
         return response()->json($announcements);
     }

}
