<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Enums\RoomStatus;
use App\Models\Image;

class Room extends Model
{
    use SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'rooms';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'room_type_id',
        'thumbnail_path',
        'description',
        'status',
    ];

    /**
     * The attributes that should be cast to native types or value objects.
     *
     * @var array
     */
    protected $casts = [
        'status' => RoomStatus::class,
    ];

    /**
     * Get all images associated with the room.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */

    public function images(): HasMany
    {
        return $this->hasMany(Image::class);
    }

    /**
     * Get the room type that owns the Room
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function roomType(): BelongsTo
    {
        return $this->belongsTo(RoomType::class, 'room_type_id');
    }

    /**
     * Get all booking details associated with the room.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function bookingDetails()
    {
        return $this->hasMany(BookingDetail::class);
    }
}
