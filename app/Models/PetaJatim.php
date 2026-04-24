<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PetaJatim extends Model
{
    protected $table = 'peta_jatim';
    protected $fillable = [
        'kode_kabupaten_kota',
        'nama_kabupaten_kota',
        'geojson_geometry'
    ];

    public function dataStatistik()
    {
        return $this->hasMany(DataPerkembangan::class, 'kode_kabupaten_kota', 'kode_kabupaten_kota');
    }
}