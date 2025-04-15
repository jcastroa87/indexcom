<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Index extends Model
{
    use CrudTrait;
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'source_api_url',
        'source_api_key',
        'source_api_path',
        'is_active',
        'last_fetch_at',
        'fetch_frequency', // in minutes
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'last_fetch_at' => 'datetime',
    ];

    /**
     * Get the rates for this index.
     */
    public function rates(): HasMany
    {
        return $this->hasMany(Rate::class);
    }

    /**
     * Get the latest rate for this index.
     */
    public function latestRate()
    {
        return $this->rates()->latest()->first();
    }

    /**
     * Get the route key for the model.
     */
    public function getRouteKeyName(): string
    {
        return 'slug';
    }
}
