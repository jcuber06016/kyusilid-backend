<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class announcement extends Model
{
    use HasFactory;

    // UserController.php
    public function welcome($an_id) {
    $announcement = announcement::find(1);
    return view('announcement.show', ['announcement' => $announcement]);
    
    
}

}


