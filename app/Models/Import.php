<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

// Kelas ini harus extends dari model Import bawaan Filament
class Import extends \Filament\Actions\Imports\Models\Import
{
    use HasFactory, HasUlids; // <-- Kunci utamanya ada di sini

    // Beritahu Laravel bahwa ID bukan angka yang bertambah
    public $incrementing = false;

    // Beritahu Laravel bahwa tipe ID adalah string
    protected $keyType = 'string';

    // Definisikan kolom yang boleh diisi
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
