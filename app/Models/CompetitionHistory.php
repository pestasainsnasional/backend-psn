<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class CompetitionHistory extends Model implements HasMedia
{
    use HasFactory, HasUlids, InteractsWithMedia;

    protected $appends = ['image_urls'];
    protected $hidden = ['media'];

    protected $fillable = [
        'history_id',
        'competition_name',
        'competition_type',
        'winner_name',
        'winner_school',
        'winner_photo_url',
        'rank',
    ];

    public $incrementing = false;
    protected $keyType = 'string';

    public function history()
    {
        return $this->belongsTo(History::class, 'history_id');
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('winner_photos')
            ->acceptsMimeTypes(['image/jpg', 'image/jpeg', 'image/png'])
            ->singleFile();
    }

    public function getImageUrlsAttribute()
    {
        return $this->getMedia('winner_photos')->map(function ($media) {
            return $media->original_url;
        });
    }
}
