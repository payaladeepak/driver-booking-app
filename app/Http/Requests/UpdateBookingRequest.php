<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateBookingRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     * For now allow if authenticated; later replace with policies for fine-grained control.
     *
     * @return bool
     */
    public function authorize()
    {
        return $this->user() !== null;
    }

    /**
     * Validation rules for updating a booking.
     * Use 'sometimes' so partial updates are allowed.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'customer_name'  => 'sometimes|required|string|max:191',
            'customer_phone' => 'sometimes|required|string|max:30',
            'pickup_address' => 'sometimes|required|string',
            'drop_address'   => 'nullable|string',
            'booking_date'   => 'sometimes|required|date',
            'vehicle_type'   => 'nullable|string|max:100',
            'driver_id'      => 'nullable|integer|exists:drivers,id',
            'status'         => ['nullable', Rule::in(['pending','accepted','in_progress','completed','cancelled'])],
            // include any other updatable fields your schema has (service_type, amount, scheduled_at etc.)
        ];
    }

    /**
     * Prepare input for validation (optional sanitization).
     */
    protected function prepareForValidation()
    {
        if ($this->has('customer_phone')) {
            $this->merge([
                'customer_phone' => trim($this->input('customer_phone')),
            ]);
        }
    }

    /**
     * Custom messages (optional)
     */
    public function messages()
    {
        return [
            'booking_date.date' => 'Booking date must be a valid date.',
            'driver_id.exists' => 'Selected driver does not exist.',
        ];
    }
}
