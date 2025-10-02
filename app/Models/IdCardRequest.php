<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;


class IdCardRequest extends Model
{
    use HasFactory;

    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'user_id',
        'request_number',
        'reason',
        'additional_info',
        'photo_path',
        'status',
        'reviewed_by',
        'reviewed_at',
        'admin_feedback',
        'card_number',
        'qr_code_path',
        'generated_card_path',
        'printed_at',
        'collected_at',
        'collected_by',
        'collection_notes',
    ];

    protected $casts = [
        'reviewed_at' => 'datetime',
        'printed_at' => 'datetime',
        'collected_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->id)) {
                $model->id = Str::uuid();
            }

            if (empty($model->request_number)) {
                $model->request_number = self::generateRequestNumber();
            }
        });

        // Trigger notification when a new ID card request is created
        static::created(function ($model) {
            // Load the user relationship
            $model->load('user');

            // Notify all admins about the new request
            Notification::notifyAdminsNewIdCardRequest($model);
        });

        // Trigger notifications when status changes
        static::updated(function ($model) {
            if ($model->wasChanged('status')) {
                $model->load('user');

                switch ($model->status) {
                    case 'approved':
                        Notification::notifyIdCardApproved($model);
                        break;
                    case 'rejected':
                        if ($model->admin_feedback) {
                            Notification::notifyIdCardRejected($model, $model->admin_feedback);
                        }
                        break;
                    case 'ready':
                        Notification::notifyIdCardReady($model);
                        break;
                }
            }
        });
    }

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    // Accessors
    public function getPhotoUrlAttribute()
    {
        if ($this->photo_path) {
            return asset('storage/' . $this->photo_path);
        }
        return $this->user->photo_url ?? asset('images/default-avatar.png');
    }

    public function getStatusBadgeAttribute()
    {
        $badges = [
            'pending' => 'bg-warning',
            'approved' => 'bg-info',
            'rejected' => 'bg-danger',
            'printed' => 'bg-primary',
            'ready' => 'bg-success',
            'collected' => 'bg-secondary'
        ];

        return $badges[$this->status] ?? 'bg-secondary';
    }

    public function getStatusIconAttribute()
    {
        $icons = [
            'pending' => 'mdi-clock-outline',
            'approved' => 'mdi-check-circle',
            'rejected' => 'mdi-close-circle',
            'printed' => 'mdi-printer',
            'ready' => 'mdi-check-all',
            'collected' => 'mdi-check-bold'
        ];

        return $icons[$this->status] ?? 'mdi-help-circle';
    }

    public function getReasonLabelAttribute()
    {
        $labels = [
            'new' => 'New Student',
            'replacement' => 'Replacement',
            'lost' => 'Lost Card',
            'damaged' => 'Damaged Card',
            'name_change' => 'Name Change'
        ];

        return $labels[$this->reason] ?? ucfirst($this->reason);
    }

    public function getQrCodeUrlAttribute()
    {
        if ($this->qr_code_path) {
            return asset('storage/' . $this->qr_code_path);
        }
        return null;
    }

    public function getGeneratedCardUrlAttribute()
    {
        if ($this->generated_card_path) {
            return asset('storage/' . $this->generated_card_path);
        }
        return null;
    }

    // Helper methods
    public function isPending()
    {
        return $this->status === 'pending';
    }

    public function isApproved()
    {
        return $this->status === 'approved';
    }

    public function isRejected()
    {
        return $this->status === 'rejected';
    }

    public function isPrinted()
    {
        return $this->status === 'printed';
    }

    public function isReady()
    {
        return $this->status === 'ready';
    }

    public function isCollected()
    {
        return $this->status === 'collected';
    }

    public function canBeApproved()
    {
        return $this->status === 'pending';
    }

    public function canBeRejected()
    {
        return $this->status === 'pending';
    }

    public function canBePrinted()
    {
        return in_array($this->status, ['approved']);
    }

    public function canBeMarkedReady()
    {
        return in_array($this->status, ['approved', 'printed']);
    }

    public function canBeCollected()
    {
        return $this->status === 'ready';
    }

    // Static methods
    public static function generateRequestNumber()
    {
        $year = date('Y');
        $month = date('m');

        // Get the last request number for this month
        $lastRequest = self::where('request_number', 'like', "REQ-{$year}{$month}%")
                          ->orderBy('request_number', 'desc')
                          ->first();

        if ($lastRequest) {
            $lastNumber = intval(substr($lastRequest->request_number, -4));
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }

        return "REQ-{$year}{$month}" . str_pad($newNumber, 4, '0', STR_PAD_LEFT);
    }

    public static function generateCardNumber()
    {
        $year = date('Y');

        // Get the last card number for this year
        $lastCard = self::where('card_number', 'like', "LU{$year}%")
                       ->whereNotNull('card_number')
                       ->orderBy('card_number', 'desc')
                       ->first();

        if ($lastCard) {
            $lastNumber = intval(substr($lastCard->card_number, -6));
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }

        return "LU{$year}" . str_pad($newNumber, 6, '0', STR_PAD_LEFT);
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function scopeRejected($query)
    {
        return $query->where('status', 'rejected');
    }

    public function scopeReady($query)
    {
        return $query->where('status', 'ready');
    }

    public function scopeCollected($query)
    {
        return $query->where('status', 'collected');
    }

    // Add these methods to your IdCardRequest model (app/Models/IdCardRequest.php)

/**
 * Check if ID card can be downloaded
 */


/**
 * Get the download URL for the ID card
 */


/**
 * Check if request can be cancelled
 */




// Add these methods to your IdCardRequest model (app/Models/IdCardRequest.php)

/**
 * Check if ID card can be downloaded
 */
public function canBeDownloaded()
{
    return in_array($this->status, ['ready', 'collected']) &&
           !empty($this->generated_card_path) &&
           Storage::disk('public')->exists($this->generated_card_path);
}

/**
 * Check if request has a downloadable card
 */
public function hasDownloadableCard()
{
    return !empty($this->generated_card_path) &&
           Storage::disk('public')->exists($this->generated_card_path);
}

/**
 * Get the download URL for the ID card
 */
public function getDownloadUrlAttribute()
{
    if ($this->canBeDownloaded()) {
        return route('student.id-card.download', $this->id);
    }
    return null;
}

/**
 * Check if request can be cancelled
 */
public function canBeCancelled()
{
    return $this->status === 'pending';
}

/**
 * Get status progress percentage
 */
public function getProgressPercentageAttribute()
{
    $statusProgress = [
        'pending' => 25,
        'approved' => 50,
        'printed' => 75,
        'ready' => 90,
        'collected' => 100,
        'rejected' => 0,
    ];

    return $statusProgress[$this->status] ?? 0;
}

/**
 * Get next expected status
 */
public function getNextStatusAttribute()
{
    $nextStatus = [
        'pending' => 'Under Review',
        'approved' => 'Being Printed',
        'printed' => 'Ready for Collection',
        'ready' => 'Available for Download',
        'collected' => 'Completed',
        'rejected' => 'Request Rejected',
    ];

    return $nextStatus[$this->status] ?? 'Unknown';
}

/**
 * Get estimated completion time
 */
public function getEstimatedCompletionAttribute()
{
    if ($this->status === 'collected') {
        return 'Completed';
    }

    $estimatedDays = [
        'pending' => 2,
        'approved' => 1,
        'printed' => 0,
        'ready' => 0,
        'rejected' => 0,
    ];

    $days = $estimatedDays[$this->status] ?? 0;

    if ($days === 0) {
        return 'Available now';
    }

    return $days === 1 ? '1 day' : "{$days} days";
}

/**
 * Get file size of generated card
 */
public function getCardFileSizeAttribute()
{
    if ($this->generated_card_path && Storage::disk('public')->exists($this->generated_card_path)) {
        $bytes = Storage::disk('public')->size($this->generated_card_path);
        return $this->formatBytes($bytes);
    }
    return null;
}

/**
 * Format bytes to human readable format
 */
private function formatBytes($bytes, $precision = 2)
{
    $units = array('B', 'KB', 'MB', 'GB', 'TB');

    for ($i = 0; $bytes > 1024; $i++) {
        $bytes /= 1024;
    }

    return round($bytes, $precision) . ' ' . $units[$i];
}

/**
 * Check if card is expired (if you want to add expiration logic)
 */
public function isExpired()
{
    if (!$this->printed_at) {
        return false;
    }

    // Cards expire after 4 years
    return $this->printed_at->addYears(4)->isPast();
}

/**
 * Get card expiry date
 */
public function getExpiryDateAttribute()
{
    if (!$this->printed_at) {
        return null;
    }

    return $this->printed_at->addYears(4);
}
}
