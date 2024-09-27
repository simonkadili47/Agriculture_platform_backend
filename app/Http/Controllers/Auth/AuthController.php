<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\Models\User;
use Carbon\Carbon;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        try {
            // Validate the user input
            $validateUser = Validator::make($request->all(), [
                'full_name' => 'required',
                'email' => 'required|email|unique:users,email',
                'password' => [
                    'required',
                    'min:8',
                    'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/'
                ],
                'role' => 'required|in:farmer,buyer',
            ]);

            if ($validateUser->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Validation error',
                    'errors' => $validateUser->errors()
                ], 422);
            }

            // Create a new user
            $user = User::create([
                'full_name' => $request->full_name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role' => $request->role,
            ]);

            // Generate a random 6-digit OTP
            $otp = rand(100000, 999999);

            // Store OTP and expiration time (3 minutes from now)
            $user->otp = $otp;
            $user->otp_expires_at = Carbon::now()->addMinutes(3);
            $user->save();

            // Send OTP to the user's email
            \Mail::to($user->email)->send(new \App\Mail\OtpMail($otp));

            return response()->json([
                'status' => true,
                'message' => 'User registered successfully! Please check your email for the OTP.',
                'otp_sent' => true,
                'user_id' => $user->id // return user ID to identify in OTP verification
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'An error occurred during registration. Please try again later.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function verifyOtp(Request $request)
    {
        try {
            // Validate the OTP input
            $validateUser = Validator::make($request->all(), [
                'user_id' => 'required|exists:users,id',
                'otp' => 'required|numeric',
            ]);

            if ($validateUser->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Validation error',
                    'errors' => $validateUser->errors()
                ], 422);
            }

            // Find the user by ID
            $user = User::find($request->user_id);

            // Check if the OTP is valid and not expired
            if ($user->otp !== $request->otp || Carbon::now()->greaterThan($user->otp_expires_at)) {
                return response()->json([
                    'status' => false,
                    'message' => 'Invalid or expired OTP.',
                ], 400);
            }

            // Clear the OTP and expiration time after verification
            $user->otp = null;
            $user->otp_expires_at = null;
            $user->save();

            // Log the user in
            Auth::login($user);

            $token = $user->createToken('API TOKEN')->plainTextToken;

            return response()->json([
                'status' => true,
                'message' => 'OTP verified successfully! User logged in.',
                'token' => $token,
                'role' => $user->role,
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'An error occurred during OTP verification. Please try again later.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function login(Request $request)
    {
        try {
            // Validate the login credentials
            $validateUser = Validator::make($request->all(), [
                'email' => 'required|email',
                'password' => 'required',
            ]);

            if ($validateUser->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Validation error',
                    'errors' => $validateUser->errors()
                ], 422);
            }

            // Attempt to authenticate the user
            if (!Auth::attempt($request->only('email', 'password'))) {
                return response()->json([
                    'status' => false,
                    'message' => 'Email or password is incorrect.'
                ], 401);
            }

            $user = Auth::user();

            // Direct user to OTP verification if OTP is enabled
            if ($user->otp) {
                return response()->json([
                    'status' => false,
                    'message' => 'Please verify your OTP.',
                    'otp_required' => true
                ], 403);
            }

            $token = $user->createToken('API TOKEN')->plainTextToken;

            return response()->json([
                'status' => true,
                'message' => 'User logged in successfully!',
                'token' => $token,
                'role' => $user->role,
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'An error occurred during login. Please try again later.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function profile()
    {
        $userData = Auth::user();
        return response()->json([
            'status' => true,
            'message' => 'Profile information',
            'data' => $userData,
            'id' => Auth::user()->id,
        ], 200);
    }

    public function logout()
    {
        try {
            auth()->user()->tokens()->delete();
            return response()->json([
                'status' => true,
                'message' => 'Logout Successfully',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'An error occurred during logout.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
