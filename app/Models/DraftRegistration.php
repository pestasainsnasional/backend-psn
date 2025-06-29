<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DraftRegistration extends Model
{
    use HasFactory, HasUlids;

    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = ['user_id', 'competition_id', 'data'];
    protected $casts = ['data' => 'array'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
