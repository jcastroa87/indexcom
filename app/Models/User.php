<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, CrudTrait;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'api_key',
        'is_active',
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
            'is_active' => 'boolean',
        ];
    }

    /**
     * Generate a new API key for the user.
     *
     * @return string
     */
    public function generateApiKey()
    {
        $this->api_key = Str::random(60);
        $this->save();

        return $this->api_key;
    }

    /**
     * Get all subscriptions for the user.
     */
    public function subscriptions()
    {
        return $this->hasMany(Subscription::class);
    }

    /**
     * Get the user's active subscription if any.
     *
     * @return Subscription|null
     */
    public function activeSubscription()
    {
        return $this->subscriptions()
            ->where('status', 'active')
            ->where(function ($query) {
                $query->whereNull('ends_at')
                    ->orWhere('ends_at', '>', now());
            })
            ->latest()
            ->first();
    }

    /**
     * Check if the user has an active subscription.
     *
     * @return bool
     */
    public function hasActiveSubscription(): bool
    {
        return $this->activeSubscription() !== null;
    }

    /**
     * Get the current plan for the user.
     *
     * @return Plan|null
     */
    public function currentPlan()
    {
        $subscription = $this->activeSubscription();
        return $subscription ? $subscription->plan : null;
    }

    /**
     * Subscribe the user to a plan.
     *
     * @param Plan $plan
     * @param array $options
     * @return Subscription
     */
    public function subscribeToPlan(Plan $plan, array $options = [])
    {
        // Cancel any active subscription first
        $this->cancelActiveSubscription();

        // Create a new subscription
        $subscription = new Subscription([
            'plan_id' => $plan.id,
            'starts_at' => now(),
            'status' => 'active',
            'api_requests_today' => 0,
            'api_requests_reset_date' => now()->addDay()->startOfDay(),
        ]);

        // Apply optional attributes
        if (isset($options['ends_at'])) {
            $subscription->ends_at = $options['ends_at'];
        }

        if (isset($options['metadata'])) {
            $subscription->metadata = $options['metadata'];
        }

        $this->subscriptions()->save($subscription);

        return $subscription;
    }

    /**
     * Cancel the user's active subscription if any.
     *
     * @return bool Whether a subscription was canceled
     */
    public function cancelActiveSubscription()
    {
        $subscription = $this->activeSubscription();

        if ($subscription) {
            $subscription->status = 'canceled';
            $subscription->ends_at = now();
            $subscription->save();
            return true;
        }

        return false;
    }
}
