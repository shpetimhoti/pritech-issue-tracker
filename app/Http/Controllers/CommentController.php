<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCommentRequest;
use App\Models\Comment;
use App\Models\Issue;
use Illuminate\Http\JsonResponse;

class CommentController extends Controller
{
    public function index(Issue $issue): JsonResponse
    {
        $comments = $issue->comments()
            ->latest()
            ->paginate(5);

        return response()->json([
            'data' => $comments->through(fn (Comment $comment) => $this->commentPayload($comment))->items(),
            'meta' => [
                'current_page' => $comments->currentPage(),
                'last_page' => $comments->lastPage(),
                'per_page' => $comments->perPage(),
                'total' => $comments->total(),
            ],
            'links' => [
                'next' => $comments->nextPageUrl(),
            ],
        ]);
    }

    public function store(StoreCommentRequest $request, Issue $issue): JsonResponse
    {
        $comment = $issue->comments()->create($request->validated());

        return response()->json([
            'message' => 'Comment added successfully.',
            'comment' => $this->commentPayload($comment),
        ], 201);
    }

    /**
     * @return array{id: int, author_name: string, body: string, created_at: string|null, created_at_human: string}
     */
    private function commentPayload(Comment $comment): array
    {
        return [
            'id' => $comment->id,
            'author_name' => $comment->author_name,
            'body' => $comment->body,
            'created_at' => $comment->created_at?->toISOString(),
            'created_at_human' => $comment->created_at?->diffForHumans() ?? 'Just now',
        ];
    }
}
