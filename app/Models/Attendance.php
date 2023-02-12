<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    protected $table ='attendance_form';
    protected $primaryKey ='attendance_id';
    protected $fillable = ['date_created', 'main_duration', 'grace_period'];
    public $timestamps = false;
    use HasFactory;
}
