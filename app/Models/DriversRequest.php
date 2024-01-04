<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DriversRequest extends Model
{
    protected $table = 'drivers_request_status';
    protected $guarded = [''];
    use HasFactory;
}
