<?php

namespace App\GraphQL\Mutations;

use Exception;
use App\Models\Link;
use Illuminate\Support\Arr;
use App\Services\LinkService;

final class ShortenedLinkMutator
{
    /**
     * @param  null  $_
     * @param  array{}  $args
     */
    public function __invoke($_, array $args)
    {
        $originalLink = $args['link'];
        $shortenedLink = (new LinkService)->makeShortenedLink();
        $linkModel = Link::create([
            'original' => $originalLink,
            'shortened' => $shortenedLink
        ]);
        return $linkModel;
    }
}
