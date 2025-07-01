<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Testimonial extends Model implements HasMedia
{
    use HasFactory, HasUlids, InteractsWithMedia;

    protected $appends = ['image_urls'];
    protected $hidden = ['media'];
    
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'name',
        'description',
        'institution',
    ];

    public function getImageUrlsAttribute()
    {
        return $this->getMedia('avatar')->map(function ($media) {
            return $media->original_url;
        });
    }
}
