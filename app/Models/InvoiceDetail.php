<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class InvoiceDetail extends Model
{
    use SoftDeletes;

    protected $table = 'invoice_details';
    protected $fillable = [
        'booking_id',
        'service_id',
        'name',
        'price',
        'quantity',
    ];
}
