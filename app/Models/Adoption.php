<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Adoption extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'adoptions';

    protected $fillable = ['from', 'pet_id', 'to'];

    public function owner()
    {
        return $this->belongsTo(User::class, 'from', 'id');
    }

    public function pet()
    {
        return $this->belongsTo(Pet::class, 'pet_id', 'id');
    }

    public function newOwner()
    {
        return $this->belongsTo(User::class, 'to', 'id');
    }
}
