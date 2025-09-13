<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Booking extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * Table name (optional if follows convention)
     *
     * protected $table = 'bookings';
     */

    /**
     * Mass-assignable attributes.
     * Keep these in sync with the DB columns and what you allow from API.
     */
    protected $fillable = [
        'customer_id',
        'customer_name',
        'customer_phone',
        'pickup_address',
        'drop_address',
        'booking_date',
        'vehicle_type',
        'driver_id',
        'status',
        'booking_code',
    ];

    /**
     * Casts
     */
    protected $casts = [
        'booking_date' => 'datetime',
    ];

    /**
     * Default attributes
     */
    protected $attributes = [
        'status' => 'pending',
    ];

    /**
     * Relationship: booking belongs to driver
     */
    public function driver()
    {
        return $this->belongsTo(Driver::class, 'driver_id');
    }

    /**
     * Relationship: booking belongs to customer (User)
     */
    public function customer()
    {
        // adjust User namespace if different
        return $this->belongsTo(\App\Models\User::class, 'customer_id');
    }
}
