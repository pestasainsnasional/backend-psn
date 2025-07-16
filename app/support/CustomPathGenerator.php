<?php
namespace App\Support;

use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\MediaLibrary\Support\PathGenerator\PathGenerator;

class CustomPathGenerator implements PathGenerator
{
    public function getPath(Media $media): string
    {
        switch ($media->collection_name) {
            case 'competition_logo':
                return 'competition-logos/';

            case 'logo_sponsor':
                return 'sponsor-logos/';

            case 'avatar':
            case 'avatars':
                return 'avatars/';

            case 'documentation':
                return 'documentation/';

            case 'winner_photos':
                return 'winner-photos/';

            case 'student-proofs':
                return 'student-proofs/';

            case 'twibbon-proofs':
                return 'twibbon-proofs/';

            case 'payment-proofs':
                return 'payment-proofs/';
            default:
                return $media->created_at->format('Y/m') . '/' . $media->id . '/';
        }
    }

    public function getPathForConversions(Media $media): string
    {
        return $this->getPath($media) . 'conversions/';
    }

    public function getPathForResponsiveImages(Media $media): string
    {
        return $this->getPath($media) . 'responsive-images/';
    }
}