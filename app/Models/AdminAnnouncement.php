<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdminAnnouncement extends Model
{
    protected $table = 'admin_ann';
    protected $primaryKey = 'admin_an_id';
    use HasFactory;
}
