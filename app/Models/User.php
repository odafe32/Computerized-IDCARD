<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasFactory, Notifiable;

    /**
     * Indicates if the model should use UUIDs.
     */
    public $incrementing = false;
    protected $keyType = 'string';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'matric_no',
        'role',
        'department',
        'photo',
        'phone',
        'status',
        'last_login_at',
        'last_login_ip',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
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
            'last_login_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->id)) {
                $model->id = Str::uuid();
            }
        });
    }

    // ========================================
    // RELATIONSHIPS
    // ========================================

    /**
     * Get all ID card requests for this user.
     */
    public function idCardRequests()
    {
        return $this->hasMany(IdCardRequest::class);
    }

    /**
     * Get the latest ID card request for this user.
     */
    public function latestIdCardRequest()
    {
        return $this->hasOne(IdCardRequest::class)->latestOfMany();
    }

    /**
     * Get the current active ID card request for this user.
     */
    public function currentIdCardRequest()
    {
        return $this->hasOne(IdCardRequest::class)
                    ->whereIn('status', ['approved', 'printed', 'ready'])
                    ->latest();
    }

    /**
     * Get ID card requests that this user has reviewed (for admin users).
     */
    public function reviewedIdCardRequests()
    {
        return $this->hasMany(IdCardRequest::class, 'reviewed_by');
    }

    /**
     * Get notifications for this user.
     */
    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }

    /**
     * Get unread notifications for this user.
     */
    public function unreadNotifications()
    {
        return $this->hasMany(Notification::class)->where('read_at', null);
    }

    // ========================================
    // ROLE & STATUS METHODS
    // ========================================

    /**
     * Check if user is an admin.
     */
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    /**
     * Check if user is a student.
     */
    public function isStudent(): bool
    {
        return $this->role === 'student';
    }

    /**
     * Check if user account is active.
     */
    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    /**
     * Check if user account is inactive.
     */
    public function isInactive(): bool
    {
        return $this->status === 'inactive';
    }

    /**
     * Check if user account is suspended.
     */
    public function isSuspended(): bool
    {
        return $this->status === 'suspended';
    }

    // ========================================
    // ID CARD RELATED METHODS
    // ========================================

    /**
     * Check if user has any ID card requests.
     */
    public function hasIdCardRequests(): bool
    {
        return $this->idCardRequests()->exists();
    }

    /**
     * Check if user has a pending ID card request.
     */
    public function hasPendingIdCardRequest(): bool
    {
        return $this->idCardRequests()->where('status', 'pending')->exists();
    }

    /**
     * Check if user has an approved ID card.
     */
    public function hasApprovedIdCard(): bool
    {
        return $this->idCardRequests()
                    ->whereIn('status', ['approved', 'printed', 'ready', 'collected'])
                    ->exists();
    }

    /**
     * Check if user has a ready-to-collect ID card.
     */
    public function hasReadyIdCard(): bool
    {
        return $this->idCardRequests()->where('status', 'ready')->exists();
    }

    /**
     * Get the user's current ID card status.
     */
    public function getIdCardStatus(): ?string
    {
        $latestRequest = $this->latestIdCardRequest;
        return $latestRequest ? $latestRequest->status : null;
    }

    /**
     * Get the user's current ID card number.
     */
    public function getIdCardNumber(): ?string
    {
        $approvedRequest = $this->idCardRequests()
                               ->whereNotNull('card_number')
                               ->whereIn('status', ['approved', 'printed', 'ready', 'collected'])
                               ->latest()
                               ->first();

        return $approvedRequest ? $approvedRequest->card_number : null;
    }

    // ========================================
    // ACCESSOR METHODS
    // ========================================

    /**
     * Get the user's full photo URL.
     */
    public function getPhotoUrlAttribute(): string
    {
        if ($this->photo) {
            // Check if it's a URL (for seeded data)
            if (filter_var($this->photo, FILTER_VALIDATE_URL)) {
                return $this->photo;
            }
            // Local storage path
            return asset('storage/' . $this->photo);
        }

        // Return default avatar
        return asset('images/default-avatar.png');
    }

    /**
     * Get the user's display name.
     */
    public function getDisplayNameAttribute(): string
    {
        return $this->name;
    }

    /**
     * Get the user's initials.
     */
    public function getInitialsAttribute(): string
    {
        $words = explode(' ', $this->name);
        $initials = '';

        foreach ($words as $word) {
            if (!empty($word)) {
                $initials .= strtoupper(substr($word, 0, 1));
            }
        }

        return substr($initials, 0, 2); // Return max 2 initials
    }

    /**
     * Get formatted department name.
     */
    public function getFormattedDepartmentAttribute(): string
    {
        return $this->department ? ucwords(strtolower($this->department)) : 'Not Assigned';
    }

    // ========================================
    // QUERY SCOPES
    // ========================================

    /**
     * Scope to get only active users.
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope to get users by role.
     */
    public function scopeByRole($query, $role)
    {
        return $query->where('role', $role);
    }

    /**
     * Scope to get only students.
     */
    public function scopeStudents($query)
    {
        return $query->where('role', 'student');
    }

    /**
     * Scope to get only admins.
     */
    public function scopeAdmins($query)
    {
        return $query->where('role', 'admin');
    }

    /**
     * Scope to get users by department.
     */
    public function scopeByDepartment($query, $department)
    {
        return $query->where('department', $department);
    }

    /**
     * Scope to search users by name, email, or matric number.
     */
    public function scopeSearch($query, $search)
    {
        return $query->where(function($q) use ($search) {
            $q->where('name', 'like', "%{$search}%")
              ->orWhere('email', 'like', "%{$search}%")
              ->orWhere('matric_no', 'like', "%{$search}%");
        });
    }

    // ========================================
    // UTILITY METHODS
    // ========================================

    /**
     * Get user's full name with matric number.
     */
    public function getFullIdentifierAttribute(): string
    {
        return $this->name . ($this->matric_no ? " ({$this->matric_no})" : '');
    }

    /**
     * Check if user can request a new ID card.
     */
    public function canRequestIdCard(): bool
    {
        // Students can request if they don't have a pending request
        if (!$this->isStudent()) {
            return false;
        }

        return !$this->hasPendingIdCardRequest();
    }

    /**
     * Get the count of ID card requests.
     */
    public function getIdCardRequestsCountAttribute(): int
    {
        return $this->idCardRequests()->count();
    }

    /**
     * Get the count of unread notifications.
     */
    public function getUnreadNotificationsCountAttribute(): int
    {
        return $this->unreadNotifications()->count();
    }
}
