<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CompetitionTimeline extends Model
{
    use HasFactory, HasUlids;

    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'competition_id',
        'title',
        'start_date',
        'end_date',
        'description',
        'order',
    ];

    public function competition()
    {
        return $this->belongsTo(Competition::class);
    }
}
