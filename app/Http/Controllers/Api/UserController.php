<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\BookmarkResource;
use App\Http\Resources\FeedPostResource;
use App\Http\Resources\UserResource;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function profile(Request $request)
    {
        return response()->json([
            'success' => true,
            'user' => new UserResource($request->user()),
        ]);
    }

    public function posts(Request $request)
    {
        $posts = $request->user()->feedPosts()
            ->with(['author', 'media'])
            ->latest('published_at')
            ->get();

        return response()->json([
            'success' => true,
            'data' => FeedPostResource::collection($posts),
        ]);
    }

    public function bookmarks(Request $request)
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
}
