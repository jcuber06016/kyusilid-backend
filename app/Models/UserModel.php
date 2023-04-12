<?php
namespace App\Models;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;


class UserModel extends Authenticatable
{       
    use HasApiTokens, HasFactory, Notifiable;
    
    protected $table = 'login';
    protected $primaryKey = 'acc_id';
    protected $fillable = ['acc_username', 'acc_password', 'acc_email','usertype','profile_pic','firstname','lastname','middle','suffix','title','first_login','profile_pic'];
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


