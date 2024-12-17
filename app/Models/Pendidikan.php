<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pendidikan extends Model
{
    use HasFactory;

    protected $table = 'pendidikan';
    protected $primaryKey = 'tingkat';
    public $timestamps = false;
    public $incrementing = false;

    protected $fillable = [
        'tingkat',
        'indek',
        'gapok1',
        'kenaikan',
        'maksimal',
    ];
}
