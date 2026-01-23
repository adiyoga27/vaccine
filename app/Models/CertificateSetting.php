<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CertificateSetting extends Model
{
    protected $fillable = [
        'kepala_upt_name',
        'kepala_upt_signature',
        'petugas_jurim_name',
        'petugas_jurim_signature',
        'background_image',
    ];

    /**
     * Get the current certificate settings (singleton pattern)
     */
    public static function current()
    {
        return static::first() ?? new static([
            'kepala_upt_name' => 'SABRI, SKM',
            'kepala_upt_signature' => '/images/signature_sabri.png',
            'petugas_jurim_name' => 'Endang Junaela, S.ST.,NS',
            'petugas_jurim_signature' => '/images/signature_endang.png',
            'background_image' => '/images/certificate_background.png',
        ]);
    }
}
