<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClaimHistory extends Model
{
    protected $fillable = [
        'claim_id',
        'user_id',
        'status',
        'note',
    ];

    public function claim()
    {
        return $this->belongsTo(Claim::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
