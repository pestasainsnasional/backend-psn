<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Team extends Model implements HasMedia
{
    use HasFactory, HasUlids, InteractsWithMedia;

    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'name',
        'school_name',
        'school_email',
        'npsn',
        'companion_teacher_name',
        'companion_teacher_contact',
        'companion_teacher_nip',
    ];

  
    public function teamMembers(): HasMany
    {
        return $this->hasMany(TeamMember::class);
    }

    public function registration(): HasOne
    {
        return $this->hasOne(Registration::class);
    }
}