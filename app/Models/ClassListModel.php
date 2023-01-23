<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;


class ClassListModel extends Authenticatable
{
protected $table = 'classes';
protected $primarykey = 'class_id';
protected $fillable = ['sub_name' , 'pf_firstname' , 'pf_lastname' , 'profile_pic' , 'day_label' , 'sched_from' , 'sched_to'];
public $timestamps= false;


public function login(){
    return $this->hasOne(Login::class, 'acc_id' , 'acc_id');
}

public function professor()
{
    return $this->hasOne(Professor::class, 'acc_id', 'acc_id');
}

public function days(){
    return $this->hasOne(Professor::class, 'day_id', 'day_id');
}

public function classes(){
    return $this->hasOne(Professor::class, 'classes_id', 'classes_id');
}


}

class Login extends Model{
    protected $table = 'login';
    protected $primarykey = 'acc_id';
    protected $fillable = ['profile_pic'];

    public function temp1()
    {
        return $this-> belongsTo(ClassListModel:: class , 'acc_id' , 'acc_id');
    }
}

class Professor extends Model
{
    protected $table = 'professor';
    protected $primaryKey = 'pf_id';
    protected $fillable = ['pf_firstname' , 'pf_lastname'];

    public function temp1()
    {
        return $this->belongsTo(ClassListModel::class, 'acc_id', 'acc_id');
    }
}

class Days extends Model{
    protected $table = 'days';
    protected $primaryKey = 'day_id';
    protected $fillable = ['day_label'];

    public function temp1()
    {
        return $this->belongsTo(ClassListModel::class, 'day_id', 'day_id');
    }

}


class Classes extends Model{
    protected $table = 'days';
    protected $primaryKey = 'day_id';
    protected $fillable = ['day_label' ];

    public function temp1()
    {
        return $this->belongsTo(ClassListModel::class, 'classes_id', 'classes_id');
    }

    
}






