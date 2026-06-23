<?php

namespace App\Policies;

use App\Models\Project;
use App\Models\User;

class ProjectPolicy
{
    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, Project $project): bool
    {
        return $project->user_id !== null && $project->user_id === $user->id;
    }

    public function delete(User $user, Project $project): bool
    {
        return $this->update($user, $project);
    }
}
