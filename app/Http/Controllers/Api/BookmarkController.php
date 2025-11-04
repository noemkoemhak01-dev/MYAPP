<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ArticleResource;
use App\Models\Article;
use App\Models\Bookmark;
use Illuminate\Http\Request;

class BookmarkController extends Controller
{
    /**
     * Get user's bookmarked articles
     */
    public function index(Request $request)
    {
        $user = $request->user();
        
        $bookmarks = $user->bookmarkedArticles()
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

    /**
     * Add article to bookmarks
     */
    public function store(Request $request, $articleId)
    {
        $article = Article::findOrFail($articleId);
        $user = $request->user();

        // Check if already bookmarked
        $exists = Bookmark::where('user_id', $user->id)
            ->where('article_id', $articleId)
            ->exists();

        if ($exists) {
            return response()->json([
                'success' => false,
                'message' => 'Article already bookmarked'
            ], 422);
        }

        Bookmark::create([
            'user_id' => $user->id,
            'article_id' => $articleId,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Article bookmarked successfully',
            'data' => [
                'isBookmarked' => true
            ]
        ]);
    }

    /**
     * Remove article from bookmarks
     */
    public function destroy(Request $request, $articleId)
    {
        $user = $request->user();

        $bookmark = Bookmark::where('user_id', $user->id)
            ->where('article_id', $articleId)
            ->first();

        if (!$bookmark) {
            return response()->json([
                'success' => false,
                'message' => 'Bookmark not found'
            ], 404);
        }

        $bookmark->delete();

        return response()->json([
            'success' => true,
            'message' => 'Bookmark removed successfully',
            'data' => [
                'isBookmarked' => false
            ]
        ]);
    }

    /**
     * Toggle bookmark
     */
    public function toggle(Request $request, $articleId)
    {
        $article = Article::findOrFail($articleId);
        $user = $request->user();

        $bookmark = Bookmark::where('user_id', $user->id)
            ->where('article_id', $articleId)
            ->first();

        if ($bookmark) {
            // Remove bookmark
            $bookmark->delete();
            $isBookmarked = false;
            $message = 'Bookmark removed successfully';
        } else {
            // Add bookmark
            Bookmark::create([
                'user_id' => $user->id,
                'article_id' => $articleId,
            ]);
            $isBookmarked = true;
            $message = 'Article bookmarked successfully';
        }

        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => [
                'isBookmarked' => $isBookmarked
            ]
        ]);
    }
}