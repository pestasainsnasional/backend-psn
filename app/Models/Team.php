<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Team extends Model
{
    use HasFactory, HasUlids;

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

    public function documentTeam(): HasOne
    {
        return $this->hasOne(DocumentTeam::class);
    }

    public function registration(): HasOne
    {
        return $this->hasOne(Registration::class);
    }
}