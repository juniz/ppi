<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class AuditPengelolaanLinenKotor extends Model
{
    use HasFactory;

    protected $table = 'audit_pengelolaan_linen_kotor';
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
