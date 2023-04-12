<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeptModel extends Model{
    protected $table = 'department';
    protected $primaryKey = 'dep_id';
    protected $fillable = ['dep_name'];
    public $timestamps = false;


    public function temp()
    {
        return $this->belongsTo(UserModel::class, 'dep_id', 'dep_id');
    }
}