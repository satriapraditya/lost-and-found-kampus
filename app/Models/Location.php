<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Location extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
    ];

    // Satu lokasi memiliki banyak laporan
    public function reports()
    {
        return $this->hasMany(Report::class);
    }
}