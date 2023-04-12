<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Section extends Model
{
    use HasFactory;
    protected $table = 'section';
    protected $primaryKey ='sec_id';
    protected $fillable = ['sec_name'];
    public $timestamps = false;
}
