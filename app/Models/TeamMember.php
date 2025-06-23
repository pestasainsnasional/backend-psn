<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TeamMember extends Model
{
    use HasFactory, HasUlids;


    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'teams_id',
        'participants_id',
        'role',
    ];

    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class, 'teams_id');
    }

    public function participant(): BelongsTo
    {
        return $this->belongsTo(Participant::class, 'participants_id');
    }
}