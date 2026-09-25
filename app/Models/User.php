<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'nim',
        'study_program',
        'email',
        'password',
        'role',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    // User memiliki banyak laporan
    public function reports()
    {
        return $this->hasMany(Report::class);
    }

    // User dapat memiliki banyak klaim
    public function claims()
    {
        return $this->hasMany(Claim::class);
    }

    // User memiliki banyak notifikasi
    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }

    // User memiliki banyak riwayat laporan
    public function reportHistories()
    {
        return $this->hasMany(ReportHistory::class);
    }
}