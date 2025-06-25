<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Competition extends Model
{
    use HasFactory, HasUlids;

 
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'competition_type_id',
        'name',
        'description',
        'rules',
        'major',
        'is_active',
    ];

    protected function casts(): array
    {
        return [ 'is_active' => 'boolean', ];
    }

    public function competitionType(): BelongsTo
    {
        return $this->belongsTo(CompetitionType::class, 'competition_type_id');
    }

    public function registrations(): HasMany
    {
        return $this->hasMany(Registration::class);
    }
}