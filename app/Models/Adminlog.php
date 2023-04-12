<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Adminlog extends Model
{
    use HasFactory;
    protected $table = 'adminlog';
    protected $primaryKey = 'log_id';
}
