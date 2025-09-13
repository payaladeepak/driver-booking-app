<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreBookingRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     * For now allow if authenticated; later replace with policies if needed.
     *
     * @return bool
     */
    public function authorize()
    {
        // If you use auth:sanctum routes, authenticated users are allowed.
        return $this->user() !== null;
    }

    /**
     * Validation rules for creating a booking.
     *
     * Keep this file as the single source of truth for store-validation.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'customer_name'  => 'required|string|max:191',
            'customer_phone' => 'required|string|max:30',
            'pickup_address' => 'required|string',
            'drop_address'   => 'nullable|string',
            'booking_date'   => 'required|date',
            'vehicle_type'   => 'nullable|string|max:100',
            // optional: driver_id is allowed but usually assigned by staff
            'driver_id'      => 'nullable|integer|exists:drivers,id',
            'status'         => 'nullable|string|in:pending,accepted,in_progress,completed,cancelled',
        ];
    }

    /**
     * Customize messages (optional but helpful).
     *
     * @return array
     */
    public function messages()
    {
        return [
            'customer_name.required' => 'Customer name is required.',
            'customer_phone.required' => 'Customer phone is required.',
            'pickup_address.required' => 'Pickup address is required.',
            'booking_date.required'   => 'Booking date is required and must be a valid date.',
            'driver_id.exists' => 'Selected driver does not exist.',
        ];
    }

    /**
     * Sanitize / prepare input before validation if needed.
     *
     * @return void
     */
    protected function prepareForValidation()
    {
        // Example: trim phone and normalize other fields
        if ($this->has('customer_phone')) {
            $this->merge([
                'customer_phone' => trim($this->input('customer_phone')),
            ]);
        }
    }
}
