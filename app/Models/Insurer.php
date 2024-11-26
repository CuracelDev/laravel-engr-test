<?php

namespace App\Models;

use App\Repositories\BaseRepository;
use App\Traits\UUID;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Insurer extends Model
{
    use HasFactory, UUID;
    protected $guarded = ['id'];
} 