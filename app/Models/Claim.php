<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Claim extends Model
{
    use HasFactory;

    protected $fillable = [
        'report_id',
        'user_id',
        'claim_description',
        'proof_description',
        'status',
        'admin_note',
    ];

    // Klaim untuk satu laporan
    public function report()
    {
        return $this->belongsTo(Report::class);
    }

    // Klaim dilakukan oleh satu user
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}