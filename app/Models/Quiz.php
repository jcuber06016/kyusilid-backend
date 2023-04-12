<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Quiz extends Model
{
    use HasFactory;
    protected $table = 'quiz';
    protected $fillable = [ 'title','description','question'];

    public $timestamps = false;

    protected $casts = [
        'question' => 'array',
    ];

}
