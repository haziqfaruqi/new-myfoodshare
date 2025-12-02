<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tracking extends Model
{
    use HasFactory;

    protected $fillable = [
        'match_id',
        'status',
        'notes',
        'location_data',
        'status_changed_at',
        'created_at',
        'updated_at',
    ];

    protected function casts(): array
    {
        return [
            'location_data' => 'array',
            'status_changed_at' => 'datetime',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    public function match()
    {
        return $this->belongsTo(\App\Models\FoodMatch::class);
    }
}