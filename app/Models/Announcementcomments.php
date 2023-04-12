<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Announcementcomments extends Model
{
    protected $table = 'announcementcomments';
    protected $primaryKey = 'com_id';
    use HasFactory;
}
