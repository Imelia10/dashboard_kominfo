<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DataPerkembangan extends Model
{
    protected $table = 'data_perkembangan';

    public function peta()
    {
        return $this->belongsTo(PetaJatim::class, 'kode_kabupaten_kota', 'kode_kabupaten_kota');
    }
}