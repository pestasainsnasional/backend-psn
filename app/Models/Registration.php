<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Notifications\RegistrationVerified;
use Illuminate\Database\Eloquent\SoftDeletes;

class Registration extends Model
{
    use HasFactory, HasUlids, SoftDeletes;

    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'user_id',
        'participant_id',
        'competition_id',
        'competition_batch',
        'team_id',
        'payment_unique_code',
        'payment_code_expires_at',
        'status',
        
    ];

    protected $casts = [
        'payment_code_expires_at' => 'datetime',
    ];


    protected static function booted(): void
    {
        static::updated (function(Registration $registration)
        {
            if ($registration -> isDirty('status')&& $registration-> status === 'verified'){
                $registration->user->notify(new RegistrationVerified($registration));
            }
        
        });

        static::deleting(function (Registration $registration) {
        
        $statusesThatTookSlot = ['draft_step_3', 'draft_step_4', 'pending', 'verified'];
        if (in_array($registration->status, $statusesThatTookSlot)) {
            if ($registration->competition_batch !== 'regular') {
                $competitionType = $registration->competition?->competitionType;
                if ($competitionType) {
                    $competitionType->increment('slot_remaining');
                }
            }
        }
        
        if ($registration->team) {
            $registration->team->delete();
        }
    });

        static::restoring(function (Registration $registration) {
            $teamToRestore = $registration->team()->withTrashed()->first();
            if ($teamToRestore) {
                $teamToRestore->restore();
            }
        });
    }
    
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function participant(): BelongsTo
    {
        return $this->belongsTo(Participant::class, 'participant_id');
    }

    public function competition(): BelongsTo
    {
        return $this->belongsTo(Competition::class, 'competition_id');
    }

    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class, 'team_id');
    }
}