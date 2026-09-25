<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReportHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'report_id',
        'user_id',
        'status',
        'note',
    ];

    // Riwayat berkaitan dengan satu laporan
    public function report()
    {
        return $this->belongsTo(Report::class);
    }

    // Riwayat dibuat oleh satu user
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}