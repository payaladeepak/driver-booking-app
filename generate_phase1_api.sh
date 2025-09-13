#!/bin/bash
# File: /home/u608956572/domains/wow.dukandar.online/public_html/wow/generate_phase1_api.sh
# Purpose: Generate Phase-1 Controllers, Requests and API Resources (Laravel artisan)
# NOTE: This script only runs artisan make commands. Review before executing.

set -e

echo "Running Phase-1 generator... (will create Controllers, Requests, Resources)"
PROJECT_ROOT="/home/u608956572/domains/wow.dukandar.online/public_html/wow"

cd "$PROJECT_ROOT" || { echo "Project root not found: $PROJECT_ROOT"; exit 1; }
#!/bin/bash
# File: /home/u608956572/domains/wow.dukandar.online/public_html/wow/generate_phase1_api.sh
# Purpose: Generate Phase-1 Controllers, Requests and API Resources (Laravel artisan)
# NOTE: This script only runs artisan make commands. Review before executing.

set -e

echo "Running Phase-1 generator... (will create Controllers, Requests, Resources)"
PROJECT_ROOT="/home/u608956572/domains/wow.dukandar.online/public_html/wow"

cd "$PROJECT_ROOT" || { echo "Project root not found: $PROJECT_ROOT"; exit 1; }

# --- Controllers (api style) ---
php artisan make:controller Api/AuthController --api
php artisan make:controller Api/UserController --api
php artisan make:controller Api/DriverController --api
php artisan make:controller Api/RoleController --api
php artisan make:controller Api/BookingController --api

# --- Form Requests (validation) ---
php artisan make:request StoreUserRequest
php artisan make:request UpdateUserRequest
php artisan make:request StoreDriverRequest
php artisan make:request UpdateDriverRequest
php artisan make:request AssignDriverRequest
php artisan make:request StoreBookingRequest
php artisan make:request UpdateBookingRequest

# --- API Resources (transformers) ---
php artisan make:resource UserResource
php artisan make:resource DriverResource
php artisan make:resource BookingResource
php artisan make:resource RoleResource

echo "Generation commands executed. Files created under app/Http/Controllers/Api, app/Http/Requests, app/Http/Resources."

echo
echo "NEXT (manual) STEPS — READ BEFORE RUN:"
echo "1) Open and edit the generated Controllers to implement methods (index, show, store, update, destroy) and dependency-inject Services/Models as required."
echo "2) Edit the Form Requests to add rules and authorize logic."
echo "3) Edit Resources to return the fields you need."
echo "4) Add routes in routes/api.php (example snippet provided below)."

cat <<'ROUTESAMPLE'

/*
| Phase-1 API routes
*/
Route::prefix('v1')->group(function () {
    // Auth
    Route::post('login', [\App\Http\Controllers\Api\AuthController::class, 'login']);
    Route::post('register', [\App\Http\Controllers\Api\AuthController::class, 'register']);

    // Protected
    Route::middleware('auth:sanctum')->group(function () {
        Route::apiResource('users', \App\Http\Controllers\Api\UserController::class);
        Route::apiResource('drivers', \App\Http\Controllers\Api\DriverController::class);
        Route::apiResource('bookings', \App\Http\Controllers\Api\BookingController::class);
        Route::apiResource('roles', \App\Http\Controllers\Api\RoleController::class);

        Route::post('bookings/{booking}/assign', [\App\Http\Controllers\Api\BookingController::class, 'assign']);
    });
});

ROUTESAMPLE

echo
echo "SCRIPT READY. (Execution will create files using artisan)."
exit 0

# --- Controllers (api style) ---
php artisan make:controller Api/AuthController --api
php artisan make:controller Api/UserController --api
php artisan make:controller Api/DriverController --api
php artisan make:controller Api/RoleController --api
php artisan make:controller Api/BookingController --api

# --- Form Requests (validation) ---
php artisan make:request StoreUserRequest
php artisan make:request UpdateUserRequest
php artisan make:request StoreDriverRequest
php artisan make:request UpdateDriverRequest
php artisan make:request AssignDriverRequest
php artisan make:request StoreBookingRequest
php artisan make:request UpdateBookingRequest

# --- API Resources (transformers) ---
php artisan make:resource UserResource
php artisan make:resource DriverResource
php artisan make:resource BookingResource
php artisan make:resource RoleResource

echo "Generation commands executed. Files created under app/Http/Controllers/Api, app/Http/Requests, app/Http/Resources."

echo
echo "NEXT (manual) STEPS — READ BEFORE RUN:"
echo "1) Open and edit the generated Controllers to implement methods (index, show, store, update, destroy) and dependency-inject Services/Models as required."
echo "2) Edit the Form Requests to add rules and authorize logic."
echo "3) Edit Resources to return the fields you need."
echo "4) Add routes in routes/api.php (example snippet provided below)."
echo
echo "EXAMPLE routes/api.php snippet (paste manually):"
cat <<'ROUTESAMPLE'

/*
| Phase-1 API routes
*/
Route::prefix('v1')->group(function () {
    // Auth
    Route::post('login', [\App\Http\Controllers\Api\AuthController::class, 'login']);
    Route::post('register', [\App\Http\Controllers\Api\AuthController::class, 'register']);

    // Protected
    Route::middleware('auth:sanctum')->group(function () {
        Route::apiResource('users', \App\Http\Controllers\Api\UserController::class);
        Route::apiResource('drivers', \App\Http\Controllers\Api\DriverController::class);
        Route::apiResource('bookings', \App\Http\Controllers\Api\BookingController::class);
        Route::apiResource('roles', \App\Http\Controllers\Api\RoleController::class);

        Route::post('bookings/{booking}/assign', [\App\Http\Controllers\Api\BookingController::class, 'assign']);
    });
});

ROUTESAMPLE

echo
echo "SCRIPT READY. (I will pause now — do not run unless ready.)"
exit 0
