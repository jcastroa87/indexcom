<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ApiRequestLog extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'subscription_id',
        'endpoint',
        'method',
        'ip_address',
        'response_code',
        'response_time',
        'user_agent',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'response_time' => 'float',
        'response_code' => 'integer',
    ];

    /**
     * Get the user that owns the API request.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the subscription associated with the API request.
     */
    public function subscription(): BelongsTo
    {
        return $this->belongsTo(Subscription::class);
    }

    /**
     * Scope a query to only include successful requests.
     */
    public function scopeSuccessful($query)
    {
        return $query->whereBetween('response_code', [200, 299]);
    }

    /**
     * Scope a query to only include failed requests.
     */
    public function scopeFailed($query)
    {
        return $query->where('response_code', '>=', 400);
    }

    /**
     * Scope a query to only include requests for a specific time period.
     */
    public function scopeForPeriod($query, $startDate, $endDate)
    {
        return $query->whereBetween('created_at', [$startDate, $endDate]);
    }
}
