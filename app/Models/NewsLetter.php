<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class NewsLetter extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'type',
        'species_id',
        'title',
        'content',
        'active',
    ];

    public function species()
    {
        return $this->belongsTo(Species::class, 'species_id');
    }
}
