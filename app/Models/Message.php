<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    use HasFactory;
    protected $table = ['chat_messages'];
    // public $fillable = [''];
    public function user()
    {
    return $this->belongsTo(User::class);
    }
}
