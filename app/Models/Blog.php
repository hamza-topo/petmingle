<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Blog extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['user_id', 'slug', 'title', 'content', 'media', 'active', 'publish_it_at'];

    /**
     * Casted property
     *
     * @var array
     */
    protected $casts = [
        'title' => 'array',
        'slug' => 'array',
        'content' => 'array',
        'media' => 'array'
    ];

    /**
     * Author relationship
     *
     * @return void
     */
    public function author()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
