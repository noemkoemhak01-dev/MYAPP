<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ArticleResource;
use App\Http\Resources\ArticleCollection;
use App\Models\Article;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

class ArticleController extends Controller
{
    /**
     * Get all articles with filters and pagination
     */
    public function index(Request $request)
    {
        $query = Article::with(['category', 'author'])
            ->published();

        // Search filter
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'LIKE', "%{$search}%")
                  ->orWhere('content', 'LIKE', "%{$search}%")
                  ->orWhere('excerpt', 'LIKE', "%{$search}%");
            });
        }

        // Category filter
        if ($request->has('category') && $request->category) {
            $query->whereHas('category', function($q) use ($request) {
                $q->where('slug', $request->category)
                  ->orWhere('name', $request->category);
            });
        }

        // Sorting
        $sortBy = $request->get('sort_by', 'published_at');
        $order = $request->get('order', 'desc');
        $query->orderBy($sortBy, $order);

        // Pagination
        $limit = $request->get('limit', 15);
        $articles = $query->paginate($limit);

        return new ArticleCollection($articles);
    }

    /**
     * Get single article by ID
     */
    public function show($id)
    {
        $article = Article::with(['category', 'author'])
            ->published()
            ->findOrFail($id);

        // Check if user is authenticated and if article is bookmarked
        if (auth('sanctum')->check()) {
            $article->is_bookmarked = $article->isBookmarkedBy(auth('sanctum')->id());
        }

        return response()->json([
            'success' => true,
            'data' => new ArticleResource($article)
        ]);
    }

    /**
     * Get article by slug
     */
    public function showBySlug($slug)
    {
        $article = Article::with(['category', 'author'])
            ->published()
            ->where('slug', $slug)
            ->firstOrFail();

        // Check if user is authenticated and if article is bookmarked
        if (auth('sanctum')->check()) {
            $article->is_bookmarked = $article->isBookmarkedBy(auth('sanctum')->id());
        }

        return response()->json([
            'success' => true,
            'data' => new ArticleResource($article)
        ]);
    }

    /**
     * Get articles by category
     */
    public function byCategory($categorySlug, Request $request)
    {
        $category = Category::where('slug', $categorySlug)->firstOrFail();

        $query = Article::with(['category', 'author'])
            ->published()
            ->where('category_id', $category->id);

        $limit = $request->get('limit', 15);
        $articles = $query->paginate($limit);

        return new ArticleCollection($articles);
    }

    /**
     * Get trending articles
     */
    public function trending(Request $request)
    {
        $limit = $request->get('limit', 10);
        
        $articles = Article::with(['category', 'author'])
            ->trending()
            ->take($limit)
            ->get();

        return response()->json([
            'success' => true,
            'data' => ArticleResource::collection($articles)
        ]);
    }

    /**
     * Get latest articles
     */
    public function latest(Request $request)
    {
        $limit = $request->get('limit', 10);
        
        $articles = Article::with(['category', 'author'])
            ->latest()
            ->take($limit)
            ->get();

        return response()->json([
            'success' => true,
            'data' => ArticleResource::collection($articles)
        ]);
    }

    /**
     * Increment article views
     */
    public function incrementView($id)
    {
        $article = Article::findOrFail($id);
        $article->incrementViews();

        return response()->json([
            'success' => true,
            'message' => 'View count updated',
            'data' => [
                'views' => $article->views
            ]
        ]);
    }

    /**
     * Create new article (Admin only)
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'excerpt' => 'nullable|string',
            'category_id' => 'required|exists:categories,id',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
            'read_time' => 'nullable|integer|min:1',
            'status' => 'nullable|in:draft,published,archived',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $data = $request->except('image');
        $data['author_id'] = auth()->id();

        // Handle image upload
        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('articles', 'public');
            $data['image_url'] = url('storage/' . $path);
        }

        $article = Article::create($data);
        $article->load(['category', 'author']);

        return response()->json([
            'success' => true,
            'message' => 'Article created successfully',
            'data' => new ArticleResource($article)
        ], 201);
    }

    /**
     * Update article (Admin/Author only)
     */
    public function update(Request $request, $id)
    {
        $article = Article::findOrFail($id);

        // Check if user is author or admin
        if ($article->author_id !== auth()->id() && !auth()->user()->is_admin) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'title' => 'sometimes|string|max:255',
            'content' => 'sometimes|string',
            'excerpt' => 'nullable|string',
            'category_id' => 'sometimes|exists:categories,id',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
            'read_time' => 'nullable|integer|min:1',
            'status' => 'nullable|in:draft,published,archived',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $data = $request->except('image');

        // Handle image upload
        if ($request->hasFile('image')) {
            // Delete old image if exists
            if ($article->image_url) {
                $oldPath = str_replace(url('storage/'), '', $article->image_url);
                if (Storage::disk('public')->exists($oldPath)) {
                    Storage::disk('public')->delete($oldPath);
                }
            }
            
            $path = $request->file('image')->store('articles', 'public');
            $data['image_url'] = url('storage/' . $path);
        }

        $article->update($data);
        $article->load(['category', 'author']);

        return response()->json([
            'success' => true,
            'message' => 'Article updated successfully',
            'data' => new ArticleResource($article)
        ]);
    }

    /**
     * Delete article (Admin only)
     */
    public function destroy($id)
    {
        $article = Article::findOrFail($id);

        // Delete image if exists
        if ($article->image_url) {
            $path = str_replace(url('storage/'), '', $article->image_url);
            if (Storage::disk('public')->exists($path)) {
                Storage::disk('public')->delete($path);
            }
        }

        $article->delete();

        return response()->json([
            'success' => true,
            'message' => 'Article deleted successfully'
        ]);
    }

    /**
     * Search articles
     */
    public function search(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'q' => 'required|string|min:2',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $query = $request->q;
        
        $articles = Article::with(['category', 'author'])
            ->published()
            ->where(function($q) use ($query) {
                $q->where('title', 'LIKE', "%{$query}%")
                  ->orWhere('content', 'LIKE', "%{$query}%")
                  ->orWhere('excerpt', 'LIKE', "%{$query}%");
            })
            ->orderBy('published_at', 'desc')
            ->paginate(15);

        return new ArticleCollection($articles);
    }
}