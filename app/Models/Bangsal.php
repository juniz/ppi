<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bangsal extends Model
{
    use HasFactory;

    protected $table = 'bangsal';
    protected $primaryKey = 'kd_bangsal';
    public $timestamps = false;
    public $incrementing = false;

    protected $fillable = [
        'kd_bangsal',
        'nm_bangsal',
        'status',
    ];
}
