<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class History extends Model implements HasMedia
{
    use HasFactory, HasUlids, InteractsWithMedia;

    protected $appends = ['image_urls'];
    protected $hidden = ['media'];

    protected $fillable = [
        'theme',
        'description',
        'season_year',
        'total_participants',
        'total_competitions',
        'documentation_url',
    ];

    public $incrementing = false;
    protected $keyType = 'string';

    public function competitionHistory()
    {
        return $this->hasMany(CompetitionHistory::class, 'history_id');
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('documentation')
            ->acceptsMimeTypes(['image/jpg', 'image/jpeg', 'image/png']);
    }

    public function getImageUrlsAttribute()
    {
        return $this->getMedia('documentation')->map(function ($media) {
            return $media->original_url;
        });
    }
}
