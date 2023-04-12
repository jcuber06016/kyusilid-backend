<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    protected $table = 'student';
    protected $primaryKey = 'stud_acc_id';
    protected $fillable = ['stud_no','acc_id'];
    public $timestamps = false;

    public function user()
    {
        return $this->belongsTo(UserModel::class, 'acc_id', 'acc_id');
    }

}