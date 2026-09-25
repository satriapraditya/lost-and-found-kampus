<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'category_id',
        'location_id',
        'type',
        'item_name',
        'description',
        'special_features',
        'event_date',
        'event_time',
        'status',
        'rejection_reason',
        'approved_at',
        'completed_at',
    ];

    protected $casts = [
        'event_date' => 'date',
        'approved_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    // Laporan dibuat oleh satu user
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Laporan memiliki satu kategori
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    // Laporan memiliki satu lokasi
    public function location()
    {
        return $this->belongsTo(Location::class);
    }

    // Laporan memiliki banyak gambar
    public function images()
    {
        return $this->hasMany(ReportImage::class);
    }

    // Laporan dapat memiliki banyak klaim
    public function claims()
    {
        return $this->hasMany(Claim::class);
    }

    // Laporan memiliki banyak riwayat
    public function histories()
    {
        return $this->hasMany(ReportHistory::class);
    }
}