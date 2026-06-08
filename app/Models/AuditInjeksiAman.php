<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class AuditInjeksiAman extends Model
{
    use HasFactory;

    protected $table = 'audit_injeksi_aman';
    protected $primaryKey = 'tanggal';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'tanggal',
        'id_ruang',
        'audit1',
        'audit2',
        'audit3',
        'audit4',
        'audit5',
        'audit6',
        'audit7',
        'audit8',
        'audit9',
        'audit10',
        'audit11',
        'audit12',
        'audit13',
        'audit14',
        'audit15',
        'audit16',
        'audit17',
        'audit18',
    ];

    public function __construct(array $attributes = [])
    {
        $this->setRawAttributes([
            'tanggal' => Carbon::now()->format('Y-m-d H:i:s'),
        ], true);

        parent::__construct($attributes);
    }

    public function ruangAuditKepatuhan()
    {
        return $this->belongsTo(RuangAuditKepatuhan::class, 'id_ruang', 'id_ruang');
    }
}
