<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserRuangAuditKepatuhan extends Model
{
    use HasFactory;

    protected $table = 'user_ruang_audit_kepatuhan';

    protected $fillable = [
        'user_id',
        'id_ruang',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function ruangAuditKepatuhan(): BelongsTo
    {
        return $this->belongsTo(RuangAuditKepatuhan::class, 'id_ruang', 'id_ruang');
    }
}
