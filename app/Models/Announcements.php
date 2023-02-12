<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class Announcements extends Model
{
    use HasFactory;
    public $timestamps = false;


    protected $table = "announcement";
    protected $primaryKey = 'an_id';


  
}
