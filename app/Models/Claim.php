<?php

namespace App\Models;

use App\Traits\UUID;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Claim extends Model
{
    use HasFactory, UUID;
    protected $guarded = ['id'];

    public function provider()
    {
        return $this->belongsTo(Provider::class);
    }

    public function insurer()
    {
        return $this->belongsTo(Insurer::class);
    }

    public function specialty()
    {
        return $this->belongsTo(Specialty::class);
    }

}