<?php

use Tests\TestCase;
use App\Models\Link;
use App\Services\LinkService;

uses(TestCase::class)->in(__DIR__);

it('makeShortenedLink test', function () {
    $linkLastRecord = Link::latest()->first();
    $uniquePath = config('services.part_name_for_short_link');
    if ($linkLastRecord) {
        $uniquePath .= "/" . $linkLastRecord->id + 1;
    } else {
        $uniquePath .= "/" . 1;
    }
    $shortenedLink = config('app.url') . "/" . $uniquePath;
    $linkService = new LinkService();
    $this->assertEquals($shortenedLink, $linkService->makeShortenedLink());
});
