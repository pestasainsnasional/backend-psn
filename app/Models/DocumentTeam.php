<?php
// File: app/Models/DocumentTeam.php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class DocumentTeam extends Model implements HasMedia
{
    use HasFactory, HasUlids, InteractsWithMedia;

    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'team_id',
    ];

    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }
}