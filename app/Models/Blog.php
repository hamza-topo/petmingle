<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Blog extends Model
{
    use HasFactory, SoftDeletes;
    protected $fillable = ['user_id', 'title', 'content', 'media', 'active'];

    /**
     * Casted property
     *
     * @var array
     */
    protected $casts = [
        'title' => 'array',
        'content' => 'array',
        'media' => 'array'
    ];
}
