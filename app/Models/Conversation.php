<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Conversation extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'first_user_id',
        'seconde_user_id',
    ];

    public function messages()
    {
        return $this->hasMany(Message::class);
    }

    public function latestMessage()
    {
        return $this->hasOne(Message::class)
            ->latestOfMany();
    }

    public function firstUser()
    {
        return $this->belongsTo(
            User::class,
            'first_user_id'
        );
    }

    public function secondUser()
    {
        return $this->belongsTo(
            User::class,
            'seconde_user_id'
        );
    }
}
