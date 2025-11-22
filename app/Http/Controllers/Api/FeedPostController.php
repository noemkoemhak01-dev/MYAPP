<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\FeedPostResource;
use App\Http\Resources\FeedTopicResource;
use App\Models\FeedPost;
use App\Models\FeedTopic;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class FeedPostController extends Controller
{
    public function index(Request $request)
    {
        $query = FeedPost::with(['author', 'media'])
            ->published();

        if ($tab = $request->string('tab')->toString()) {
            $query = match ($tab) {
                'popular' => $query->orderByDesc('like_count')->orderByDesc('view_count'),
                'news' => $query->where('type', 'news_feed')->latest('published_at'),
                'following' => $request->user()
                    ? $query->where('user_id', $request->user()->id)
                    : $query->orderByDesc('published_at'),
                default => $query->orderByDesc('is_trending')->orderByDesc('published_at'),
            };
        }

        if ($topic = $request->string('topic')->toString()) {
            $query->where('topic', $topic);
        }

        if ($type = $request->string('type')->toString()) {
            $query->ofType($type);
        }

        if ($request->boolean('trending')) {
            $query->trending();
        }

        if ($search = $request->string('q')->toString()) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('topic', 'like', "%{$search}%")
                    ->orWhere('content', 'like', "%{$search}%");
            });
        }

        if ($request->boolean('bookmarked') && $request->user()) {
            $query->whereHas('bookmarks', fn ($q) => $q->where('user_id', $request->user()->id));
        }

        $posts = $query
            ->orderByDesc('published_at')
            ->orderByDesc('created_at')
            ->paginate($request->integer('per_page', 10))
            ->withQueryString();

        return FeedPostResource::collection($posts)
            ->additional([
                'meta' => [
                    'current_page' => $posts->currentPage(),
                    'last_page' => $posts->lastPage(),
                    'per_page' => $posts->perPage(),
                    'total' => $posts->total(),
                ],
            ]);
    }

    public function trending(Request $request)
    {
        $limit = max(1, min(50, $request->integer('limit', 6)));

        $posts = FeedPost::with(['author', 'media'])
            ->trending()
            ->orderByDesc('view_count')
            ->orderByDesc('published_at')
            ->limit($limit)
            ->get();

        return response()->json([
            'success' => true,
            'data' => FeedPostResource::collection($posts),
        ]);
    }

    public function latest(Request $request)
    {
        $limit = max(1, min(50, $request->integer('limit', 6)));

        $posts = FeedPost::with(['author', 'media'])
            ->published()
            ->latest('published_at')
            ->limit($limit)
            ->get();

        return response()->json([
            'success' => true,
            'data' => FeedPostResource::collection($posts),
        ]);
    }

    public function showBySlug(string $slug)
    {
        $post = FeedPost::with(['author', 'media', 'comments'])
            ->where('slug', $slug)
            ->firstOrFail();

        return response()->json([
            'success' => true,
            'data' => new FeedPostResource($post),
        ]);
    }

    public function search(Request $request)
    {
        $request->validate([
            'q' => ['required', 'string', 'min:2'],
        ]);

        $term = $request->string('q')->toString();

        $posts = FeedPost::with(['author', 'media'])
            ->published()
            ->where(function ($q) use ($term) {
                $q->where('title', 'like', "%{$term}%")
                    ->orWhere('topic', 'like', "%{$term}%")
                    ->orWhere('content', 'like', "%{$term}%");
            })
            ->orderByDesc('published_at')
            ->paginate($request->integer('per_page', 10))
            ->withQueryString();

        return FeedPostResource::collection($posts)
            ->additional([
                'meta' => [
                    'current_page' => $posts->currentPage(),
                    'last_page' => $posts->lastPage(),
                    'per_page' => $posts->perPage(),
                    'total' => $posts->total(),
                ],
            ]);
    }

    public function topics()
    {
        $topics = FeedTopic::orderByDesc('trend_score')
            ->orderBy('name')
            ->get();

        return response()->json([
            'success' => true,
            'data' => FeedTopicResource::collection($topics),
        ]);
    }

    public function show(FeedPost $post)
    {
        $post->loadMissing(['author', 'media', 'comments']);

        return response()->json([
            'success' => true,
            'data' => new FeedPostResource($post),
        ]);
    }

    public function store(Request $request)
    {
        $data = $this->validatePost($request);

        $post = FeedPost::create([
            ...$data,
            'user_id' => $request->user()->id,
            'published_at' => $data['published_at'] ?? now(),
        ]);

        $post->load(['author', 'media']);

        return response()->json([
            'success' => true,
            'message' => 'Post created successfully',
            'data' => new FeedPostResource($post),
        ], 201);
    }

    public function update(Request $request, FeedPost $post)
    {
        $this->authorizePost($request, $post);

        $data = $this->validatePost($request, true);
        $post->update($data);
        $post->load(['author', 'media']);

        return response()->json([
            'success' => true,
            'message' => 'Post updated successfully',
            'data' => new FeedPostResource($post),
        ]);
    }

    public function destroy(Request $request, FeedPost $post)
    {
        $this->authorizePost($request, $post);

        $post->delete();

        return response()->json([
            'success' => true,
            'message' => 'Post removed',
        ]);
    }

    public function toggleLike(Request $request, FeedPost $post)
    {
        $post->like_count = $post->like_count + ($request->boolean('increment', true) ? 1 : -1);
        if ($post->like_count < 0) {
            $post->like_count = 0;
        }
        $post->save();

        return response()->json([
            'success' => true,
            'likeCount' => $post->like_count,
        ]);
    }

    public function incrementView(FeedPost $post)
    {
        $post->increment('view_count');

        return response()->json([
            'success' => true,
            'viewCount' => $post->view_count,
        ]);
    }

    protected function authorizePost(Request $request, FeedPost $post): void
    {
        $user = $request->user();
        if (! $user || ($user->id !== $post->user_id && ! $user->is_admin)) {
            abort(403, 'You do not have permission to modify this post');
        }
    }

    protected function validatePost(Request $request, bool $isUpdate = false): array
    {
        $rules = [
            'title' => [$isUpdate ? 'sometimes' : 'required', 'string', 'max:255'],
            'topic' => ['nullable', 'string', 'max:120'],
            'category' => ['nullable', 'string', 'max:120'],
            'type' => ['nullable', Rule::in(['news_feed', 'video', 'article'])],
            'excerpt' => ['nullable', 'string'],
            'content' => ['nullable', 'string'],
            'video_url' => ['nullable', 'string', 'max:255'],
            'is_featured' => ['nullable', 'boolean'],
            'is_trending' => ['nullable', 'boolean'],
            'is_published' => ['nullable', 'boolean'],
            'published_at' => ['nullable', 'date'],
            'cover_image' => [$isUpdate ? 'sometimes' : 'nullable', 'file', 'image', 'max:5120'],
        ];

        $data = $request->validate($rules);

        if ($request->hasFile('cover_image')) {
            $path = $request->file('cover_image')->store('feed', 'public');
            $data['cover_image'] = $path;
        }

        return $data;
    }
}
