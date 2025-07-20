<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Import extends \Filament\Actions\Imports\Models\Import
{
    use HasFactory, HasUlids; 

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'user_id',
        'file_name',
        'file_path',
        'importer',
        'total_rows',
        'completed_at',
        'processed_rows',
        'successful_rows',
    ];

    // Definisikan relasi ke User
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
