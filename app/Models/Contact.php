<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class Contact extends Model
{ 

    protected $table = 'contacts';

    protected $fillable = [
        'user_id',
        'guest_name', 
        'guest_email', 
        'title', 
        'content',
        'content_reply',
        'status'
    ];

    public function user()
{
    return $this->belongsTo(User::class, 'user_id');
}
}
