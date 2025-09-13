<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class DriverController extends Controller
{
    /**
     * List drivers (users with role = driver). Supports search & pagination.
     */
    public function index(Request $request)
    {
        $query = User::where('role', 'driver');

        if ($request->filled('q')) {
            $q = $request->q;
            $query->where(function($qry) use ($q) {
                $qry->where('name', 'like', "%{$q}%")
                    ->orWhere('email', 'like', "%{$q}%")
                    ->orWhere('phone', 'like', "%{$q}%")
                    ->orWhere('vehicle_number', 'like', "%{$q}%")
                    ->orWhere('license_number', 'like', "%{$q}%");
            });
        }

        $perPage = (int) $request->get('per_page', 15);
        $drivers = $query->orderBy('id', 'desc')->paginate($perPage);

        return response()->json(['success' => true, 'data' => $drivers], 200);
    }

    /**
     * Store a new driver (force role = driver).
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|unique:users,email',
            'phone' => 'nullable|string|unique:users,phone',
            'password' => 'required|string|min:6',
            // driver specific fields (optional)
            'vehicle_number' => 'nullable|string|max:50',
            'license_number' => 'nullable|string|max:100',
        ]);

        $validated['password'] = Hash::make($validated['password']);
        $validated['role'] = 'driver';

        $driver = User::create($validated);

        return response()->json(['success' => true, 'data' => $driver], 201);
    }

    /**
     * Show driver details
     */
    public function show($id)
    {
        $driver = User::where('role', 'driver')->find($id);

        if (! $driver) {
            return response()->json(['success' => false, 'message' => 'Driver not found.'], 404);
        }

        return response()->json(['success' => true, 'data' => $driver], 200);
    }

    /**
     * Update driver (role remains driver).
     */
    public function update(Request $request, $id)
    {
        $driver = User::where('role', 'driver')->find($id);

        if (! $driver) {
            return response()->json(['success' => false, 'message' => 'Driver not found.'], 404);
        }

        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'email' => ['sometimes','nullable','email', Rule::unique('users','email')->ignore($driver->id)],
            'phone' => ['sometimes','nullable','string', Rule::unique('users','phone')->ignore($driver->id)],
            'password' => 'sometimes|nullable|string|min:6',
            'vehicle_number' => 'sometimes|nullable|string|max:50',
            'license_number' => 'sometimes|nullable|string|max:100',
            // prevent changing role via this controller
        ]);

        if (isset($validated['password']) && $validated['password']) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        // ensure role remains driver
        $validated['role'] = 'driver';

        $driver->update($validated);

        return response()->json(['success' => true, 'data' => $driver], 200);
    }

    /**
     * Delete driver
     */
    public function destroy($id)
    {
        $driver = User::where('role', 'driver')->find($id);

        if (! $driver) {
            return response()->json(['success' => false, 'message' => 'Driver not found.'], 404);
        }

        $driver->delete();

        return response()->json(['success' => true, 'message' => 'Driver deleted.'], 200);
    }
}
