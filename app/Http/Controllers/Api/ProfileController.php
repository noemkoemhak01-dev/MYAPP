<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\BookmarkResource;
use App\Http\Resources\DashboardSnapshotResource;
use App\Http\Resources\FeedPostResource;
use App\Http\Resources\UserResource;
use App\Models\DashboardSnapshot;
use App\Models\FeedPost;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    public function show(Request $request)
    {
        $user = $request->user()->loadCount(['feedPosts', 'bookmarks']);

        $recentPosts = FeedPost::with(['author', 'media'])
            ->where('user_id', $user->id)
            ->latest('published_at')
            ->limit(5)
            ->get();

        $bookmarks = $user->bookmarks()
            ->with(['post.author', 'post.media'])
            ->latest()
            ->limit(5)
            ->get();

        return response()->json([
            'success' => true,
            'user' => new UserResource($user),
            'recentPosts' => FeedPostResource::collection($recentPosts),
            'bookmarks' => BookmarkResource::collection($bookmarks),
        ]);
    }

    public function activity(Request $request)
    {
        $snapshot = DashboardSnapshot::with('breakdowns')
            ->where(function ($q) use ($request) {
                $q->whereNull('user_id')
                    ->orWhere('user_id', $request->user()->id);
            })
            ->latest()
            ->first();

        if (! $snapshot) {
            return response()->json([
                'success' => true,
                'data' => null,
            ]);
        }

        return response()->json([
            'success' => true,
            'data' => new DashboardSnapshotResource($snapshot),
        ]);
    }
}
