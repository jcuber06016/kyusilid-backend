<?php
namespace App\Models;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;


class UserModel extends Authenticatable
{       
    protected $table = 'login';
    protected $primaryKey = 'acc_id';
    protected $fillable = ['acc_email','acc_username', 'acc_password','usertype','temp'];
    public $timestamps = false;


    public function student()
    {
        return $this->hasOne(Student::class, 'acc_id', 'acc_id');
    }

    public function professor()
    {
        return $this->hasOne(Professor::class, 'acc_id', 'acc_id');
    }

    public function department()
    {
        return $this->hasOne(Department::class, 'dep_id', 'dep_id');
    }
}

class Student extends Model
{
    protected $table = 'student';
    protected $primaryKey = 'stud_id';
    protected $fillable = ['stud_no'];

    public function user()
    {
        return $this->belongsTo(UserModel::class, 'acc_id', 'acc_id');
    }

}

    class Professor extends Model
{
    protected $table = 'professor';
    protected $primaryKey = 'pf_id';
    protected $fillable = ['dep_id'];

    public function user()
    {
        return $this->belongsTo(UserModel::class, 'acc_id', 'acc_id');
    }
}




class DeptModel extends Authenticatable{
    protected $table = 'department';
    protected $primaryKey = 'dep_id';
    protected $fillable = ['dep_name'];

    public function temp()
    {
        return $this->belongsTo(UserModel::class, 'dep_id', 'dep_id');
    }
}
