<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use Filament\Panel;
use Filament\Models\Contracts\FilamentUser;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\MediaLibrary\HasMedia; 
use Spatie\MediaLibrary\InteractsWithMedia; 

class User extends Authenticatable implements FilamentUser, MustVerifyEmail, HasMedia
{
    use HasApiTokens, HasUlids, HasRoles, HasFactory, Notifiable, InteractsWithMedia;

    protected $fillable = [
        'name',
        'email',
        'password',
        'email_verified_at',
    ];

    protected $with = ['roles'];
    public $incrementing = false;
    protected $keyType = 'string';
    protected $hidden = ['password','remember_token',];
    protected $appends =['avatar_url'];


    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function canAccessPanel(Panel $panel): bool
    {
        return $this->hasRole('admin') && $this->hasVerifiedEmail();
    }

    public function registerMediaCollections(): void
    {
        $this
            ->addMediaCollection('avatars')
            ->singleFile(); 
    }

    public function getAvatarUrlAttribute()
    {
        if ($this->hasMedia('avatars')){
        return $this->getFirstMediaUrl('avatars');
        }

        return asset('images/default-profile-avatar.png');
    }

    public function createdCompetitionTypes(): HasMany
    {
        return $this->hasMany(CompetitionType::class, 'created_by');  
    }
    public function registrations(): HasMany
    {
        return $this->hasMany(Registration::class);
    }

     public function drafts(): HasMany
    {
        return $this->hasMany(DraftRegistration::class); 
    }
}