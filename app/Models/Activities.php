<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Activities extends Model
{
    protected $table = 'activity';
    use HasFactory;
    public $timestamps = false;
}
