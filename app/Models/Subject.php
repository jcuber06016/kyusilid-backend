<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subject extends Model
{
    protected $table='subject';
    protected $primaryKey='sub_id';
    
     public $timestamps = false;
    public function department()
    {
        return $this->hasOne(Department::class, 'dep_id', 'dep_id');
    }
}
