<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids; // <-- PENTING
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

// Ganti nama kelas agar sesuai dengan standar Filament
class Export extends \Filament\Actions\Exports\Models\Export 
{
    use HasFactory, HasUlids; // <-- PENTING

    // Beritahu Laravel bahwa ID bukan angka yang bertambah
    public $incrementing = false;

    // Beritahu Laravel bahwa tipe ID adalah string
    protected $keyType = 'string';

    // Definisikan fillable jika diperlukan (opsional untuk kasus ini)
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

    // Definisikan relasi ke User
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
