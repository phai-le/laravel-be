<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RoomType extends Model
{
    use SoftDeletes;

    protected $table = 'room_types';
    protected $fillable = [
        'name',
        'price'
    ];

    /**
     * The room properties that belong to the RoomType
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function roomProperties()
    {
        return $this->hasMany(RoomProperty::class);
    }

    /**
     * The properties that belong to the RoomType
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function properties()
    {
        return $this->belongsToMany(Property::class, 'room_properties');
    }

    /**
     * Get the rooms that belongs to the room type.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function rooms()
    {
        return $this->hasMany(Room::class);
    }

    /**
     * Get limited room properties for each room type
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function roomPropertiesLimited()
    {
        return $this->hasMany(RoomProperty::class)
            ->orderBy('property_id', 'desc')
            ->limit(3);
    }
}
