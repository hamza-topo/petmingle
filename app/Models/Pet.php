<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Pet extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * fillable
     *
     * @var array
     */
    protected $fillable = ['user_id', 'species_id', 'race_id', 'name', 'age', 'sexe', 'color', 'images', 'about'];

    protected $casts = [
        'images' => 'array',
    ];

    public function owner()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function species()
    {
        return $this->belongsTo(Species::class, 'species_id');
    }

    public function race()
    {
        return $this->belongsTo(Race::class);
    }

    public function locations()
    {
        return $this->hasMany(Location::class);
    }
}
