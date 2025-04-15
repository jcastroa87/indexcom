<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Model;

class Subscription extends Model
{
    use CrudTrait;
    //

    /**
     * Fix for the relationship handling in admin panel
     */
    public function plan()
    {
        return $this->belongsTo(\App\Models\Plan::class, 'plan_id');
    }

    /**
     * Fix for the relationship handling in admin panel
     */
    public function user()
    {
        return $this->belongsTo(\App\Models\User::class, 'user_id');
    }

    /**
     * Send a notification to the user about their subscription.
     *
     * @param string $type The type of notification (created, renewed, canceled, expiring_soon, limit_warning)
     * @return void
     */
    public function notifyUser(string $type): void
    {
        $this->user->notify(new \App\Notifications\SubscriptionNotification($this, $type));
    }
}
