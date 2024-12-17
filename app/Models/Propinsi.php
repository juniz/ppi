<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Propinsi extends Model
{
    use HasFactory;

    protected $table = 'propinsi';
    protected $primaryKey = 'kd_prop';
    public $timestamps = false;
    public $incrementing = false;
}
