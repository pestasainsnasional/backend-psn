<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Illuminate\Database\Eloquent\SoftDeletes;

class Competition extends Model implements HasMedia
{
    use HasFactory, HasUlids, InteractsWithMedia, SoftDeletes;

    public $incrementing = false;
    protected $keyType = 'string';
    protected $appends = ['image_urls'];
    protected $hidden = ['media'];

    protected $fillable = [
        'competition_type_id',
        'name',
        'description',
        'guidebook_link',
        'rules',
        'major',
        'is_active',
    ];

    protected static function booted(): void
    {

        static::deleting(function (Competition $competition) {
            foreach ($competition->registrations as $registration) {
                $registration->delete();
            }
        });

        static::restoring(function (Competition $competition) {
            $registrationsToRestore = $competition->registrations()->withTrashed()->get();
            foreach ($registrationsToRestore as $registration) {
                $registration->restore();
            }
        });
    }


    protected function casts(): array
    {
        return ['is_active' => 'boolean',];
    }

    public function competitionType(): BelongsTo
    {
        return $this->belongsTo(CompetitionType::class, 'competition_type_id');
    }

    public function registrations(): HasMany
    {
        return $this->hasMany(Registration::class);
    }

    public function timelines()
    {
        return $this->hasMany(CompetitionTimeline::class)->orderBy('order');
    }


    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('competition_logo')
            ->acceptsMimeTypes(['image/jpg', 'image/jpeg', 'image/png'])
            ->singleFile();
    }

    public function getImageUrlsAttribute()
    {
        return $this->getMedia('competition_logo')->map(function ($media) {
            return $media->original_url;
        });
    }
}
