<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CompetitionType extends Model
{
    use HasFactory, HasUlids;

   
    public $incrementing = false;
    protected $keyType = 'string';


    protected $table = 'competition_types';

    protected $fillable = [
        'type',
        'current_batch',
        'slot_remaining',
        'price',
    ];

    protected function casts(): array
    {
        return ['price' => 'decimal:2', ];
    }

    public function competitions(): HasMany
    {
        return $this->hasMany(Competition::class);
    }
}