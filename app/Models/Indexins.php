<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Indexins extends Model
{
    use HasFactory;

    protected $table = 'indexins';
    protected $primaryKey = 'dep_id';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'dep_id',
        'persen',
    ];
}
