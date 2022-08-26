<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\Link;
use Illuminate\Http\Request;
use App\Services\LinkService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Validator;

class LinkController extends Controller
{
    public function shortenLink(Request $request)
    {
        // Валидация
        $validator = Validator::make($request->all(), [
            'link' => 'required|url|unique:links,original|max:380',
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors()->all(), 400);
        }

        $validated = $validator->validated();
        try {
            $shortenedLink = (new LinkService)->makeShortenedLink();
            Link::create([
                'original' => $validated['link'],
                'shortened' => $shortenedLink
            ]);
            if (Cache::has("all_links_list")) {
                Cache::forget('all_links_list');
            }
        } catch (Exception $e) {
            Log::error("Укорачивание ссылки: " . $e->getMessage() . "\n" . $e->getTraceAsString());
            return response()->json($e->getMessage(), 500);
        }
        return response()->json($shortenedLink);
    }

    public function getShortenLinksList()
    {
        $linksList = Cache::remember('all_links_list', now()->addMonth(), function () {
            return Link::select('original', 'shortened', 'click_count')->get();
        });
        return response()->json($linksList);
    }

    public function redirectToOriginalLink(Request $request)
    {
        $shortenedLink = $request->url();
        $originalLinkRecord = Link::where('shortened', $shortenedLink)->first();
        if (!$originalLinkRecord) {
            return response()->json("No such shortened link found", 400);
        }
        $originalLinkRecord->click_count += 1;
        $originalLinkRecord->save();
        if (Cache::has("all_links_list")) {
            Cache::forget('all_links_list');
        }
        return redirect($originalLinkRecord->original);
    }

}
