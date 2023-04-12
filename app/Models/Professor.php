<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Professor extends Model
{
    use HasFactory;
    protected $table = 'professor';
    protected $primaryKey ='prof_acc_id';
    protected $fillable =['acc_id','faculty_id'];
    public $timestamps = false;
}
