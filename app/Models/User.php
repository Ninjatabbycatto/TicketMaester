<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Filament\Models\Contracts\HasAvatar;
use Illuminate\Support\Facades\Storage;


class User extends Authenticatable implements HasAvatar

{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'firstname', 'lastname', 'email', 'mobileNum', 'address',
        'password', 'user_type', 'clinic_id', 'profile_picture', 'avatar_url',
        'custom_fields',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'custom_fields' => 'array', // cast to array

        ];
    }

    public function clinic()
    {
        return $this->belongsTo(Clinic::class);
    }

    public function getFilamentAvatarUrl(): ?string {
        if ($this->profile_picture) {
            // If the stored avatar is a full URL, return as is
            if (str_starts_with($this->profile_picture, 'http')) {
                return $this->profile_picture;
            }

            // Otherwise, assume it's a relative path in storage
            return asset('storage/' . $this->profile_picture);
        }

        // Fallback: generate UI avatar URL based on user's name
        $name = urlencode($this->name);
        return "https://ui-avatars.com/api/?name={$name}&color=7F9CF5&background=EBF4FF";
    }

    public function getAvatarUrlAttribute() {   
        if ($this->profile_picture) {
            return asset('storage/' . $this->profile_picture);
        }
        // Return default avatar URL if no profile picture is set
        return 'https://ui-avatars.com/api/?name=' . urlencode($this->name) . '&color=7F9CF5&background=EBF4FF';
    }
}   
