<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MatchTable extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'matches';

    protected $fillable = ['from', 'to'];

    public function fromPet()
    {
        return $this->belongsTo(Pet::class, 'from', 'id');
    }

    public function toPet()
    {
        return $this->belongsTo(Pet::class, 'to', 'id');
    }
}
