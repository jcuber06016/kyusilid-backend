<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Activity_assign extends Model
{
    use HasFactory;
    protected $table = 'activity_assign';
    public $timestamps = false;
    protected $primaryKey = "assign_id";
}
