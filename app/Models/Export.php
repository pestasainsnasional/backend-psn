<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids; 
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Export extends \Filament\Actions\Exports\Models\Export 
{
    use HasFactory, HasUlids; 

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'user_id',
        'exporter',
        'total_rows',
        'file_disk',
        'file_name',
        'completed_at',
        'processed_rows',
        'successful_rows',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
