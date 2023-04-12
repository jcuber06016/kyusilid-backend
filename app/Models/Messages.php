<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Messages extends Model
{

    protected $primaryKey= 'message_id';
    protected $table = 'messages';
    use HasFactory;
}
