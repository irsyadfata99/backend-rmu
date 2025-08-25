<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Http\Controllers\MemberController; // Changed from Api\MemberController

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Public routes (no authentication required)
// Registration
Route::post('/register', function (Request $request) {
    $request->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|string|email|max:255|unique:users',
        'password' => 'required|string|min:8|confirmed',
    ]);

    $user = User::create([
        'name' => $request->name,
        'email' => $request->email,
        'password' => Hash::make($request->password),
    ]);

    // Create token for the new user
    $token = $user->createToken('auth_token')->plainTextToken;

    return response()->json([
        'message' => 'User registered successfully',
        'user' => $user,
        'access_token' => $token,
        'token_type' => 'Bearer'
    ], 201);
});

// Login
Route::post('/login', function (Request $request) {
    $request->validate([
        'email' => 'required|email',
        'password' => 'required',
    ]);

    // Check credentials
    if (!Auth::attempt($request->only('email', 'password'))) {
        return response()->json([
            'message' => 'Invalid login credentials'
        ], 401);
    }

    // Get the authenticated user
    $user = User::where('email', $request->email)->firstOrFail();

    // Create token
    $token = $user->createToken('auth_token')->plainTextToken;

    return response()->json([
        'message' => 'Login successful',
        'user' => $user,
        'access_token' => $token,
        'token_type' => 'Bearer'
    ]);
});

// Public member routes (no authentication required)
Route::prefix('members')->group(function () {
    Route::get('/wilayah-options', [MemberController::class, 'getWilayahOptions']); // GET /api/members/wilayah-options
    Route::post('/next-member-id', [MemberController::class, 'getNextMemberId']); // POST /api/members/next-member-id
    Route::post('/', [MemberController::class, 'store']); // POST /api/members (public registration)
});

// Protected routes (require authentication)
Route::middleware('auth:sanctum')->group(function () {
    // Get authenticated user info
    Route::get('/user', function (Request $request) {
        return response()->json([
            'user' => $request->user()
        ]);
    });

    // Logout
    Route::post('/logout', function (Request $request) {
        // Revoke the current user's token
        $request->user()->currentAccessToken()->delete();
        
        return response()->json([
            'message' => 'Logged out successfully'
        ]);
    });

    // Protected Member routes (requires authentication for admin functions)
    Route::prefix('members')->group(function () {
        Route::get('/', [MemberController::class, 'index']); // GET /api/members (list all)
        Route::get('/stats', [MemberController::class, 'getStats']); // GET /api/members/stats
        Route::get('/{id}', [MemberController::class, 'show']); // GET /api/members/{member_id}
        Route::put('/{id}', [MemberController::class, 'update']); // PUT /api/members/{member_id}
        Route::delete('/{id}', [MemberController::class, 'destroy']); // DELETE /api/members/{member_id}
    });
});