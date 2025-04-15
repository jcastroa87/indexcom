<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Rate extends Model
{
    use CrudTrait;
    use HasFactory;

    protected $fillable = [
        'index_id',
        'date',
        'value',
        'is_manual',
    ];

    protected $casts = [
        'date' => 'date',
        'value' => 'float',
        'is_manual' => 'boolean',
    ];

    /**
     * Get the index that owns the rate.
     */
    public function index(): BelongsTo
    {
        return $this->belongsTo(Index::class);
    }
}
