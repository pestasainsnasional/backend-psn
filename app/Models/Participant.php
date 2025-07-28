<?php
// File: app/Models/Participant.php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Participant extends Model implements HasMedia
{
    use HasFactory, HasUlids, InteractsWithMedia;

    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'user_id',
        'full_name',
        'place_of_birth',
        'date_of_birth',
        'address',
        'nisn',
        'phone_number',
        'email',
    ];

    protected function casts(): array
    {
        return ['date_of_birth' => 'date',  ];
    }

    protected static function booted(): void
    {
    parent::boot();

    static::deleting(function (Participant $participant) {
        $participant->teamMembers()->each(function ($teamMember) {
            $teamMember->delete();
        });
    });
    }

    public function registerMediaCollections(): void
    {
        $this
            ->addMediaCollection('student-proofs')
            ->singleFile(); 

        $this
            ->addMediaCollection('twibbon-proofs')
            ->singleFile(); 
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function teamMembers(): HasMany
    {
        return $this->hasMany(TeamMember::class, 'participant_id');
    }
}
