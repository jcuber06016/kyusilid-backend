<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Classlist extends Model
{
    use HasFactory;

    protected $table = 'classlist';
    protected $primary = 'class_id';
    protected $fillable = ['classes_id','acc_id'];
    public $timestamps = false;

 



}
