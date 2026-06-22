<?php

namespace App\Http\Requests;

use App\Models\Issue;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreIssueRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, list<mixed>>
     */
    public function rules(): array
    {
        return [
            'project_id' => ['required', 'integer', Rule::exists('projects', 'id')],
            'title' => ['required', 'string', 'min:3', 'max:180'],
            'description' => ['required', 'string', 'min:10', 'max:5000'],
            'status' => ['required', Rule::in(Issue::STATUSES)],
            'priority' => ['required', Rule::in(Issue::PRIORITIES)],
            'due_date' => ['nullable', 'date'],
            'tag_ids' => ['nullable', 'array'],
            'tag_ids.*' => ['integer', 'distinct', Rule::exists('tags', 'id')],
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'project_id.required' => 'Choose a project for this issue.',
            'title.min' => 'Issue titles should be at least 3 characters.',
            'description.min' => 'Add a description of at least 10 characters.',
            'status.in' => 'Choose a valid issue status.',
            'priority.in' => 'Choose a valid issue priority.',
            'tag_ids.*.distinct' => 'Each tag can only be selected once.',
        ];
    }
}
