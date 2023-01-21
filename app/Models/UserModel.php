<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;


class UserModel extends Authenticatable
{       
    protected $table = 'login';
    protected $primaryKey = 'acc_id';
    protected $fillable = ['acc_email','acc_username', 'acc_password','usertype'];
    public $timestamps = false;

    // relationships
    public function role()
    {
        return $this->belongsTo(Role::class);
    }


    use HasFactory;
}
