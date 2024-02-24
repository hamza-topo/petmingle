<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Like extends Model
{
    use HasFactory, SoftDeletes;
    
    protected $fillable = ['from', 'to'];

    public function from()
    {
        return $this->belongsTo(Pet::class, 'from');
    }

    public function to()
    {
        return $this->belongsTo(Pet::class, 'to');
    }
}
