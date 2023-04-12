<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Classinfo extends Model
{
    use HasFactory;
    protected $table = 'classinfo';
    protected $primaryKey ='classes_id';
    public $timestamps = false;
}
