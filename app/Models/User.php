<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'full_name',
        'email',
        'password',
        'role',
        'otp',                // Added field to store OTP
        'otp_expires_at',      // Optional field to track OTP expiration
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'otp',                // Hide OTP in API responses
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'otp_expires_at' => 'datetime', // Cast the OTP expiration as a datetime object
    ];

    /**
     * Check if the OTP is valid (Optional)
     *
     * @return bool
     */
    public function isOtpValid($otp)
    {
        if ($this->otp !== $otp) {
            return false;
        }

        // Check if OTP has expired (if you set expiration time)
        if ($this->otp_expires_at && now()->greaterThan($this->otp_expires_at)) {
            return false;
        }

        return true;
    }
}
