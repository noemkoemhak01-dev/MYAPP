<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ArticleResource;
use App\Http\Resources\UserResource;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * Get user profile
     */
    public function profile(Request $request)
    {
        return response()->json([
            'success' => true,
            'data' => new UserResource($request->user())
        ]);
    }

    /**
     * Get user's articles
     */
    public function articles(Request $request)
    {
        $articles = $request->user()
            ->articles()
            ->with(['category', 'author'])
            ->latest()
            ->paginate(15);

        return response()->json([
            'success' => true,
            'data' => ArticleResource::collection($articles),
            'meta' => [
                'current_page' => $articles->currentPage(),
                'last_page' => $articles->lastPage(),
                'per_page' => $articles->perPage(),
                'total' => $articles->total(),
            ]
        ]);
    }

    /**
     * Get user's bookmarks
     */
    public function bookmarks(Request $request)
    {
        $bookmarks = $request->user()
            ->bookmarkedArticles()
            ->with(['category', 'author'])
            ->latest('bookmarks.created_at')
            ->paginate(15);

        return response()->json([
            'success' => true,
            'data' => ArticleResource::collection($bookmarks),
            'meta' => [
                'current_page' => $bookmarks->currentPage(),
                'last_page' => $bookmarks->lastPage(),
                'per_page' => $bookmarks->perPage(),
                'total' => $bookmarks->total(),
            ]
        ]);
    }
}