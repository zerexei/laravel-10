<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Room extends Model
{
    use HasFactory;

    public function participants()
    {
        return $this->belongsToMany(User::class)
        ->using(RoomUser::class)
        ->withTimestamps();
    }

    public function messages()
    {
        return $this->hasMany(Message::class);
    }
}
