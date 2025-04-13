<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Claim extends Model
{
    use HasFactory;

    protected $table = 'claims';

    const PRIORITES = [1, 2, 3, 4, 5];
    const SPECIALTIES = ["cardiology", "orthopedics", "neurology", "oncology", "dermatology"];
}
