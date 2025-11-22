<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\BookmarkResource;
use App\Models\Bookmark;
use App\Models\FeedPost;
use Illuminate\Http\Request;

class BookmarkController extends Controller
{
    public function index(Request $request)
    {
        $bookmarks = $request->user()->bookmarks()
            ->with(['post.author', 'post.media'])
            ->latest()
            ->get();

        return response()->json([
            'success' => true,
            'data' => BookmarkResource::collection($bookmarks),
        ]);
    }

    public function store(Request $request, FeedPost $post)
    {
        $source = trim($request->input('source', ''));

        $bookmark = Bookmark::updateOrCreate(
            [
                'user_id' => $request->user()->id,
                'feed_post_id' => $post->id,
            ],
            [
                'source' => $source !== '' ? $source : null,
            ]
        );

        return response()->json([
            'success' => true,
            'message' => 'Post bookmarked successfully',
            'data' => new BookmarkResource($bookmark->load('post.author', 'post.media')),
        ], 201);
    }

    public function destroy(Request $request, FeedPost $post)
    {
        $deleted = Bookmark::where('user_id', $request->user()->id)
            ->where('feed_post_id', $post->id)
            ->delete();

        return response()->json([
            'success' => true,
            'message' => $deleted ? 'Bookmark removed' : 'Bookmark not found',
        ]);
    }

    public function toggle(Request $request, FeedPost $post)
    {
        $bookmark = Bookmark::where('user_id', $request->user()->id)
            ->where('feed_post_id', $post->id)
            ->first();

        if ($bookmark) {
            $bookmark->delete();
            return response()->json([
                'success' => true,
                'message' => 'Bookmark removed',
            ]);
        }

        $source = trim($request->input('source', ''));

        $bookmark = Bookmark::create([
            'user_id' => $request->user()->id,
            'feed_post_id' => $post->id,
            'source' => $source !== '' ? $source : null,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Bookmark added',
            'data' => new BookmarkResource($bookmark->load('post.author', 'post.media')),
        ], 201);
    }
}
