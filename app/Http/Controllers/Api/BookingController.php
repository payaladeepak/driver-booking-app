<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Booking;
use App\Models\Driver;
use Illuminate\Validation\Rule;
use Illuminate\Database\Eloquent\Builder;
use App\Http\Requests\StoreBookingRequest;
use App\Http\Requests\UpdateBookingRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class BookingController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $perPage = (int) $request->query('per_page', 15);

        $query = Booking::query()->with(['driver']);

        if ($request->filled('status')) {
            $query->where('status', $request->query('status'));
        }
        if ($request->filled('driver_id')) {
            $query->where('driver_id', (int) $request->query('driver_id'));
        }
        if ($request->filled('date_from')) {
            $query->whereDate('booking_date', '>=', $request->query('date_from'));
        }
        if ($request->filled('date_to')) {
            $query->whereDate('booking_date', '<=', $request->query('date_to'));
        }
        if ($request->filled('q')) {
            $q = $request->query('q');
            $query->where(function (Builder $sub) use ($q) {
                $sub->where('id', $q)
                    ->orWhere('customer_name', 'like', "%{$q}%")
                    ->orWhere('customer_phone', 'like', "%{$q}%")
                    ->orWhere('pickup_address', 'like', "%{$q}%");
            });
        }

        $sortBy = $request->query('sort_by', 'created_at');
        $sortDir = strtolower($request->query('sort_dir', 'desc')) === 'asc' ? 'asc' : 'desc';
        $query->orderBy($sortBy, $sortDir);

        $paginated = $query->paginate($perPage)->appends($request->query());
        return response()->json($paginated);
    }

    public function show($id): JsonResponse
    {
        $booking = Booking::with(['driver'])->find($id);
        if (! $booking) {
            return response()->json(['message' => 'Booking not found'], 404);
        }
        return response()->json($booking);
    }

    /**
     * Store a new booking
     */
    public function store(StoreBookingRequest $request): JsonResponse
    {
        $data = $request->validated();

        $user = $request->user();
        if ($user) {
            $data['customer_id'] = $user->id;
            $data['customer_name'] = $data['customer_name'] ?? $user->name;
            $data['customer_phone'] = $data['customer_phone'] ?? $user->phone;
        }

        $data['booking_code'] = $data['booking_code'] ?? strtoupper(Str::random(12));
        $data['status'] = $data['status'] ?? 'pending';

        // Fill safe defaults for all optional DB columns
        $safeFields = [
            'customer_id'   => $data['customer_id'] ?? null,
            'customer_name' => $data['customer_name'] ?? null,
            'customer_phone'=> $data['customer_phone'] ?? null,
            'pickup_address'=> $data['pickup_address'] ?? null,
            'drop_address'  => $data['drop_address'] ?? null,
            'booking_date'  => $data['booking_date'] ?? null,
            'vehicle_type'  => $data['vehicle_type'] ?? null,
            'driver_id'     => $data['driver_id'] ?? null,
            'status'        => $data['status'],
            'booking_code'  => $data['booking_code'],

            // extra fields some schemas require
            'service_type'  => $data['service_type'] ?? 'bike',
            'amount'        => $data['amount'] ?? 0,
            'pickup'        => $data['pickup'] ?? null,
            'dropoff'       => $data['dropoff'] ?? null,
            'pickup_lat'    => $data['pickup_lat'] ?? null,
            'pickup_lng'    => $data['pickup_lng'] ?? null,
            'drop_lat'      => $data['drop_lat'] ?? null,
            'drop_lng'      => $data['drop_lng'] ?? null,
            'scheduled_at'  => $data['scheduled_at'] ?? null,
            'requested_at'  => $data['requested_at'] ?? null,
            'confirmed_at'  => $data['confirmed_at'] ?? null,
            'completed_at'  => $data['completed_at'] ?? null,
            'cancelled_at'  => $data['cancelled_at'] ?? null,
            'cancellation_reason' => $data['cancellation_reason'] ?? null,
        ];

        try {
            DB::beginTransaction();
            $booking = Booking::create($safeFields);
            DB::commit();

            $booking->load('driver');
            return response()->json($booking, 201);
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Failed to create booking',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function update(UpdateBookingRequest $request, $id): JsonResponse
    {
        $booking = Booking::find($id);
        if (! $booking) {
            return response()->json(['message' => 'Booking not found'], 404);
        }

        $validated = $request->validated();
        if (isset($validated['driver_id']) && $validated['driver_id']) {
            $driver = Driver::find($validated['driver_id']);
            if (! $driver) {
                return response()->json(['message' => 'Driver not found'], 404);
            }
        }

        $booking->update($validated);
        return response()->json($booking);
    }

    public function destroy($id): JsonResponse
    {
        $booking = Booking::find($id);
        if (! $booking) {
            return response()->json(['message' => 'Booking not found'], 404);
        }
        $booking->delete();
        return response()->json(['message' => 'Booking trashed']);
    }

    public function trashed(Request $request): JsonResponse
    {
        $perPage = (int) $request->query('per_page', 15);
        $query = Booking::onlyTrashed()->with(['driver'])->orderBy('deleted_at', 'desc');

        if ($request->filled('q')) {
            $q = $request->query('q');
            $query->where(function (Builder $sub) use ($q) {
                $sub->where('id', $q)
                    ->orWhere('customer_name', 'like', "%{$q}%")
                    ->orWhere('customer_phone', 'like', "%{$q}%");
            });
        }

        $paginated = $query->paginate($perPage)->appends($request->query());
        return response()->json($paginated);
    }

    public function restore($id): JsonResponse
    {
        $booking = Booking::onlyTrashed()->find($id);
        if (! $booking) {
            return response()->json(['message' => 'Booking not found in trashed'], 404);
        }
        $booking->restore();
        return response()->json(['message' => 'Booking restored', 'booking' => $booking]);
    }

    public function assignDriver(Request $request, $id): JsonResponse
    {
        $booking = Booking::find($id);
        if (! $booking) {
            return response()->json(['message' => 'Booking not found'], 404);
        }

        $v = validator($request->all(), [
            'driver_id' => 'required|integer|exists:drivers,id',
            'status' => ['nullable', Rule::in(['pending','accepted','in_progress','completed','cancelled'])],
        ]);

        if ($v->fails()) {
            return response()->json(['errors' => $v->errors()], 422);
        }

        $driver = Driver::find($request->input('driver_id'));
        if (! $driver) {
            return response()->json(['message' => 'Driver not found'], 404);
        }

        $booking->driver_id = $driver->id;
        $booking->status = $request->input('status', $booking->status);
        $booking->save();

        $booking->load('driver');
        return response()->json(['message' => 'Driver assigned', 'booking' => $booking]);
    }
}
