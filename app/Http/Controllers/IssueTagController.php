<?php

namespace App\Http\Controllers;

use App\Models\Issue;
use App\Models\Tag;
use Illuminate\Http\JsonResponse;

class IssueTagController extends Controller
{
    public function store(Issue $issue, Tag $tag): JsonResponse
    {
        if ($issue->tags()->whereKey($tag->id)->exists()) {
            return response()->json([
                'message' => 'Tag is already attached to this issue.',
                'tag' => $this->tagPayload($tag, true),
            ], 409);
        }

        $issue->tags()->syncWithoutDetaching([$tag->id]);

        return response()->json([
            'message' => 'Tag attached successfully.',
            'tag' => $this->tagPayload($tag, true),
        ]);
    }

    public function destroy(Issue $issue, Tag $tag): JsonResponse
    {
        $issue->tags()->detach($tag->id);

        return response()->json([
            'message' => 'Tag detached successfully.',
            'tag' => $this->tagPayload($tag, false),
        ]);
    }

    /**
     * @return array{id: int, name: string, color: string|null, attached: bool}
     */
    private function tagPayload(Tag $tag, bool $attached): array
    {
        return [
            'id' => $tag->id,
            'name' => $tag->name,
            'color' => $tag->color,
            'attached' => $attached,
        ];
    }
}
