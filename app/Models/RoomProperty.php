<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RoomProperty extends Model
{
    protected $table = 'room_properties';

    protected $fillable = ['room_type_id', 'property_id', 'value'];

    public function roomType()
    {
        return $this->belongsTo(RoomType::class);
    }

    public function property()
    {
        return $this->belongsTo(Property::class);
    }
}
