<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo; 
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\SoftDeletes;

class CompetitionType extends Model
{
    use HasFactory, HasUlids, SoftDeletes;

    public $incrementing = false;
    protected $keyType = 'string';

    protected $table = 'competition_types';

    protected $fillable = [
        'created_by', 
        'type',
        'current_batch',
        'slot_remaining',
        'price',
    ];
    
   protected static function booted(): void
    {
        parent::boot();

        static::creating(function ($model) {
            if (Auth::check()) { 
                $model->created_by = Auth::id();
            }
        });

      
        static::deleting(function (CompetitionType $competitionType) {
            foreach ($competitionType->competitions as $competition) {
                $competition->delete();
            }
        });


        static::restoring(function (CompetitionType $competitionType) {

            $competitionsToRestore = $competitionType->competitions()->withTrashed()->get();
            foreach ($competitionsToRestore as $competition) {
                $competition->restore(); 
            }
        });
    }
    
    
    protected function casts(): array
    {
        return ['price' => 'decimal:2'];
    }

    public function competitions(): HasMany
    {
        return $this->hasMany(Competition::class);
    }

    public function creator(): BelongsTo 
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
