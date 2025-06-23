<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Registration extends Model
{
    use HasFactory, HasUlids;

    public $incrementing = false;
    protected $keyType = 'string';


    protected $fillable = [
        'participants_id',
        'competitions_id',
        'teams_id',
        'status',
    ];


    public function participant(): BelongsTo
    {
       
        return $this->belongsTo(Participant::class, 'participants_id');
    }

    public function competition(): BelongsTo
    {
        return $this->belongsTo(Competition::class, 'competitions_id');
    }


    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class, 'teams_id');
    }
}