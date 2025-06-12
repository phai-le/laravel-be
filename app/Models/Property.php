<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Property extends Model
{
    protected $table = 'properties';
    protected $fillable = [
        'name'
    ];

    public function roomTypes()
    {
        return $this->belongsToMany(RoomType::class, 'room_properties');
    }
}
