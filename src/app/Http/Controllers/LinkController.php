<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateShortenLinkRequest;
use App\Http\Resources\LinkResource;
use Exception;
use App\Models\Link;
use Illuminate\Http\Request;
use App\Services\LinkService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Validator;

class LinkController extends Controller
{
    /**
     * @OA\Post(
     *      path="/shorten/link",
     *      operationId="shortenLink",
     *      tags={"Endpoints"},
     *      summary="Запрос для создания короткой ссылки",
     *      @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 @OA\Property(
     *                     property="link",
     *                     type="string"
     *                 ),
     *                 example={"link": "https://google.com"}
     *             )
     *         )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          @OA\JsonContent(
     *                  @OA\Property(property="id", type="int64", example=1),
     *                  @OA\Property(property="original", type="string", example="https://google.com"),
     *                  @OA\Property(property="shortened", type="string", example="http://localhost/shortenedlink/1"),
     *                  @OA\Property(property="updated_at", type="string", example="2022-08-28T19:37:26.000000Z"),
     *                  @OA\Property(property="created_at", type="string", example="2022-08-28T19:37:26.000000Z"),
     *          )
     *      ),
     *      @OA\Response(
     *          response=400,
     *          description="Bad Request"
     *      ),
     *      @OA\Response(
     *          response=500,
     *          description="Internal server error",
     *      )
     * )
     */
    public function shortenLink(CreateShortenLinkRequest $request)
    {
        $validated = $request->validated();
        try {
            $shortenedLink = (new LinkService)->makeShortenedLink();
            $linkModel = Link::create([
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
        return new LinkResource($linkModel);
    }

    /**
     * @OA\Get(
     *      path="/shorten/links",
     *      operationId="getShortenLinksList",
     *      tags={"Endpoints"},
     *      summary="Получить список ранее сокращенных ссылок",
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          @OA\JsonContent(
     *                 type="array",
     *                 @OA\Items(
     *                     type="object",
     *                     @OA\Property(property="original", type="string", example="https://google.com"),
     *                     @OA\Property(property="shortened", type="string", example="http://localhost/shortenedlink/1"),
     *                     @OA\Property(property="click_count", type="int", example=1),
     *                 )
     *          )
     *     )
     * )
     */
    public function getShortenLinksList()
    {
        $linksList = Cache::remember('all_links_list', now()->addMonth(), function () {
            return LinkResource::collection(Link::all());
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
