<?php

namespace App\Http\Controllers;

use App\Models\Issue;
use App\Models\User;
use Illuminate\Http\JsonResponse;

class IssueAssignmentController extends Controller
{
    public function store(Issue $issue, User $user): JsonResponse
    {
        if ($issue->users()->whereKey($user->id)->exists()) {
            return response()->json([
                'message' => 'User is already assigned to this issue.',
                'user' => $this->userPayload($issue, $user, true),
            ], 409);
        }

        $issue->users()->syncWithoutDetaching([$user->id]);

        return response()->json([
            'message' => 'User assigned successfully.',
            'user' => $this->userPayload($issue, $user, true),
        ], 201);
    }

    public function destroy(Issue $issue, User $user): JsonResponse
    {
        $issue->users()->detach($user->id);

        return response()->json([
            'message' => 'User unassigned successfully.',
            'user' => $this->userPayload($issue, $user, false),
        ]);
    }

    /**
     * @return array{id: int, name: string, email: string, assigned: bool, attach_url: string, detach_url: string}
     */
    private function userPayload(Issue $issue, User $user, bool $assigned): array
    {
        return [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'assigned' => $assigned,
            'attach_url' => route('issues.members.attach', [$issue, $user]),
            'detach_url' => route('issues.members.detach', [$issue, $user]),
        ];
    }
}
