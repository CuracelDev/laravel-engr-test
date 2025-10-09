<?php

namespace App\Models;

use App\Models\Claim;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Provider extends Model
{
    use HasFactory;

    protected $fillable = ['name'];
    public function claims() { return $this->hasMany(Claim::class); }
}
