<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RuangAuditKepatuhan extends Model
{
    use HasFactory;

    protected $table = 'ruang_audit_kepatuhan';
    protected $primaryKey = 'id_ruang';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'id_ruang',
        'nama_ruang',
    ];
}
