<?php

namespace App\Helpers;

class VideoLinkHelper
{
    /**
     * Get an embeddable preview URL (iframe src) for a Google Drive or YouTube link.
     * Returns null if the link format isn't recognized.
     */
    public static function embedUrl(?string $link): ?string
    {
        if (!$link) {
            return null;
        }

        if (preg_match('/drive\.google\.com\/file\/d\/([^\/\?]+)/', $link, $m)) {
            return 'https://drive.google.com/file/d/' . $m[1] . '/preview';
        }

        if (preg_match('/(?:youtube\.com\/watch\?v=|youtu\.be\/)([^&\?]+)/', $link, $m)) {
            return 'https://www.youtube.com/embed/' . $m[1] . '?autoplay=1';
        }

        return null;
    }

    /**
     * Get a thumbnail image + embed URL pair for a video link (Drive or YouTube).
     * Returns null if the link format isn't recognized.
     *
     * @return array{thumb: string, embed: string}|null
     */
    public static function thumbnail(?string $link): ?array
    {
        if (!$link) {
            return null;
        }

        if (preg_match('/drive\.google\.com\/file\/d\/([^\/\?]+)/', $link, $m)) {
            return [
                'thumb' => 'https://drive.google.com/thumbnail?id=' . $m[1] . '&sz=w400-h300',
                'embed' => 'https://drive.google.com/file/d/' . $m[1] . '/preview',
            ];
        }

        if (preg_match('/(?:youtube\.com\/watch\?v=|youtu\.be\/)([^&\?]+)/', $link, $m)) {
            return [
                'thumb' => 'https://img.youtube.com/vi/' . $m[1] . '/maxresdefault.jpg',
                'embed' => 'https://www.youtube.com/embed/' . $m[1] . '?autoplay=1',
            ];
        }

        return null;
    }
}
