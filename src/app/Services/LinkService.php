<?php

namespace App\Services;

use App\Models\Link;

class LinkService
{
    /**
     * Сформировать укороченную ссылку
     * @return string укороченная ссылка
     */
    public function makeShortenedLink() : string
    {
        $linkLastRecord = Link::latest()->first();
        $uniquePath = config('services.part_name_for_short_link');
        if ($linkLastRecord) {
            $uniquePath .= "/" . $linkLastRecord->id + 1;
        } else {
            $uniquePath .= "/" . 1;
        }
        $shortenedLink = config('app.url') . "/" . $uniquePath;
        return $shortenedLink;
    }
}

