@extends('layouts.app')

@section('content')
    @php
        $statusClasses = [
            'open' => 'bg-sky-50 text-sky-700',
            'in_progress' => 'bg-amber-50 text-amber-700',
            'closed' => 'bg-emerald-50 text-emerald-700',
        ];

        $priorityClasses = [
            'low' => 'bg-slate-100 text-slate-700',
            'medium' => 'bg-violet-50 text-violet-700',
            'high' => 'bg-red-50 text-red-700',
        ];
    @endphp

    <div class="mb-8 flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
        <div>
            <a href="{{ route('issues.index') }}" class="text-sm font-medium text-sky-700 hover:text-sky-800">Back to issues</a>
            <h1 class="mt-3 text-3xl font-semibold tracking-tight text-slate-950">{{ $issue->title }}</h1>
            <p class="mt-3 max-w-3xl text-sm leading-6 text-slate-700">{{ $issue->description }}</p>
        </div>

        <div class="flex gap-2">
            <a href="{{ route('issues.edit', $issue) }}" class="inline-flex items-center justify-center rounded-md border border-slate-300 bg-white px-4 py-2 text-sm font-medium text-slate-700 shadow-sm hover:bg-slate-50">
                Edit
            </a>
            <form action="{{ route('issues.destroy', $issue) }}" method="POST" onsubmit="return confirm('Delete this issue?');">
                @csrf
                @method('DELETE')
                <button type="submit" class="inline-flex items-center justify-center rounded-md bg-red-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-red-700">
                    Delete
                </button>
            </form>
        </div>
    </div>

    <section class="mb-8 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <div class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
            <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Project</p>
            <a href="{{ route('projects.show', $issue->project) }}" class="mt-2 block text-sm font-medium text-sky-700 hover:text-sky-800">{{ $issue->project->name }}</a>
        </div>
        <div class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
            <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Status</p>
            <span class="mt-2 inline-flex rounded-full px-2.5 py-1 text-xs font-medium {{ $statusClasses[$issue->status] ?? 'bg-slate-100 text-slate-700' }}">{{ Str::headline($issue->status) }}</span>
        </div>
        <div class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
            <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Priority</p>
            <span class="mt-2 inline-flex rounded-full px-2.5 py-1 text-xs font-medium {{ $priorityClasses[$issue->priority] ?? 'bg-slate-100 text-slate-700' }}">{{ Str::headline($issue->priority) }}</span>
        </div>
        <div class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
            <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Due date</p>
            <p class="mt-2 text-sm font-medium text-slate-950">{{ $issue->due_date?->format('M j, Y') ?? 'No due date' }}</p>
        </div>
    </section>

    <section class="mb-8 rounded-lg border border-slate-200 bg-white p-6 shadow-sm" data-issue-tags>
        <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-lg font-semibold text-slate-950">Manage tags</h2>
                <p class="mt-1 text-sm text-slate-600">Attach or remove existing tags without leaving this page.</p>
            </div>
            <a href="{{ route('tags.index') }}" class="text-sm font-medium text-sky-700 hover:text-sky-800">Manage tag library</a>
        </div>

        <div data-tag-error class="mt-4 hidden rounded-md border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700"></div>

        <div class="mt-5">
            <h3 class="text-sm font-semibold uppercase tracking-wide text-slate-500">Current tags</h3>
            <div data-attached-tags class="mt-3 flex flex-wrap gap-2">
                <p data-empty-attached class="{{ $issue->tags->isEmpty() ? '' : 'hidden' }} text-sm text-slate-600">This issue has no tags.</p>
            @forelse ($issue->tags as $tag)
                <span data-attached-tag data-tag-id="{{ $tag->id }}" class="inline-flex items-center gap-2 rounded-full bg-slate-100 px-3 py-1.5 text-sm font-medium text-slate-700">
                    <span class="h-2.5 w-2.5 rounded-full" style="background-color: {{ $tag->color ?? '#64748b' }}"></span>
                    <span>{{ $tag->name }}</span>
                    <button
                        type="button"
                        data-tag-action="detach"
                        data-url="{{ route('issues.tags.detach', [$issue, $tag]) }}"
                        data-tag-name="{{ $tag->name }}"
                        data-tag-color="{{ $tag->color }}"
                        class="ml-1 rounded-full px-1 text-slate-500 hover:bg-slate-200 hover:text-slate-900"
                        aria-label="Remove {{ $tag->name }} tag"
                    >
                        &times;
                    </button>
                </span>
                @empty
                    <p>No tags attached.</p>
            @endforelse
            </div>
        </div>

        <div class="mt-6 border-t border-slate-200 pt-5">
            <h3 class="text-sm font-semibold uppercase tracking-wide text-slate-500">Available tags</h3>
            <div data-available-tags class="mt-3 flex flex-wrap gap-2">
                @php
                    $availableTags = $availableTags ?? collect();
                @endphp

                <p data-empty-available class="{{ $availableTags->isEmpty() ? '' : 'hidden' }} text-sm text-slate-600">All tags are attached to this issue.</p>
                @foreach ($availableTags as $tag)
                    <button
                        type="button"
                        data-tag-id="{{ $tag->id }}"
                        data-tag-action="attach"
                        data-url="{{ route('issues.tags.attach', [$issue, $tag]) }}"
                        data-tag-name="{{ $tag->name }}"
                        data-tag-color="{{ $tag->color }}"
                        class="inline-flex items-center gap-2 rounded-full border border-slate-200 bg-white px-3 py-1.5 text-sm font-medium text-slate-700 hover:bg-slate-50"
                        aria-label="Attach {{ $tag->name }} tag"
                    >
                        <span class="h-2.5 w-2.5 rounded-full" style="background-color: {{ $tag->color ?? '#64748b' }}"></span>
                        <span>{{ $tag->name }}</span>
                        <span class="text-sky-700">Add</span>
                    </button>
                @endforeach
            </div>
        </div>
    </section>

    <section class="mb-8 rounded-lg border border-slate-200 bg-white p-6 shadow-sm" data-issue-members>
        <div>
            <h2 class="text-lg font-semibold text-slate-950">Members</h2>
            <p class="mt-1 text-sm text-slate-600">Assign or remove users without leaving this page.</p>
        </div>

        <div data-member-error class="mt-4 hidden rounded-md border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700"></div>

        <div class="mt-5">
            <h3 class="text-sm font-semibold uppercase tracking-wide text-slate-500">Assigned members</h3>
            <div data-assigned-members class="mt-3 flex flex-col gap-2">
                <p data-empty-assigned-members class="{{ $issue->users->isEmpty() ? '' : 'hidden' }} text-sm text-slate-600">No members are assigned to this issue.</p>
                @foreach ($issue->users as $user)
                    <div data-assigned-member data-user-id="{{ $user->id }}" class="flex flex-col gap-3 rounded-md bg-slate-100 px-3 py-2 sm:flex-row sm:items-center sm:justify-between">
                        <div>
                            <p class="text-sm font-medium text-slate-800">{{ $user->name }}</p>
                            <p class="text-xs text-slate-500">{{ $user->email }}</p>
                        </div>
                        <button
                            type="button"
                            data-member-action="detach"
                            data-url="{{ route('issues.members.detach', [$issue, $user]) }}"
                            data-attach-url="{{ route('issues.members.attach', [$issue, $user]) }}"
                            data-user-name="{{ $user->name }}"
                            data-user-email="{{ $user->email }}"
                            class="inline-flex items-center justify-center rounded-md border border-slate-300 bg-white px-3 py-1.5 text-xs font-medium text-slate-700 hover:bg-slate-50 disabled:cursor-not-allowed disabled:opacity-60"
                            aria-label="Remove {{ $user->name }} from issue"
                        >
                            Remove
                        </button>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="mt-6 border-t border-slate-200 pt-5">
            <h3 class="text-sm font-semibold uppercase tracking-wide text-slate-500">Available users</h3>
            <div data-available-members class="mt-3 flex flex-col gap-2">
                @php
                    $availableUsers = $availableUsers ?? collect();
                @endphp

                <p data-empty-available-members class="{{ $availableUsers->isEmpty() ? '' : 'hidden' }} text-sm text-slate-600">All users are assigned to this issue.</p>
                @foreach ($availableUsers as $user)
                    <button
                        type="button"
                        data-member-action="attach"
                        data-user-id="{{ $user->id }}"
                        data-url="{{ route('issues.members.attach', [$issue, $user]) }}"
                        data-detach-url="{{ route('issues.members.detach', [$issue, $user]) }}"
                        data-user-name="{{ $user->name }}"
                        data-user-email="{{ $user->email }}"
                        class="flex flex-col gap-1 rounded-md border border-slate-200 bg-white px-3 py-2 text-left hover:bg-slate-50 disabled:cursor-not-allowed disabled:opacity-60 sm:flex-row sm:items-center sm:justify-between"
                        aria-label="Assign {{ $user->name }} to issue"
                    >
                        <span>
                            <span class="block text-sm font-medium text-slate-800">{{ $user->name }}</span>
                            <span class="block text-xs text-slate-500">{{ $user->email }}</span>
                        </span>
                        <span class="text-sm font-medium text-sky-700">Add</span>
                    </button>
                @endforeach
            </div>
        </div>
    </section>

    <section
        class="rounded-lg border border-slate-200 bg-white p-6 shadow-sm"
        data-comments
        data-index-url="{{ route('issues.comments.index', $issue) }}"
        data-store-url="{{ route('issues.comments.store', $issue) }}"
    >
        <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
            <h2 class="text-lg font-semibold text-slate-950">Comments</h2>
            <span class="text-sm text-slate-500">
                <span data-comments-count>{{ $issue->comments_count }}</span>
                <span data-comments-label>{{ Str::plural('comment', $issue->comments_count) }}</span>
            </span>
        </div>

        <form data-comment-form class="mt-5 space-y-5 border-b border-slate-200 pb-6">
            <div>
                <label for="comment_author_name" class="block text-sm font-medium text-slate-800">Author name</label>
                <input
                    id="comment_author_name"
                    name="author_name"
                    type="text"
                    class="mt-2 block w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm shadow-sm outline-none transition focus:border-sky-500 focus:ring-2 focus:ring-sky-200"
                    maxlength="100"
                >
                <p data-comment-error="author_name" class="mt-2 hidden text-sm text-red-600"></p>
            </div>

            <div>
                <label for="comment_body" class="block text-sm font-medium text-slate-800">Comment</label>
                <textarea
                    id="comment_body"
                    name="body"
                    rows="4"
                    class="mt-2 block w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm shadow-sm outline-none transition focus:border-sky-500 focus:ring-2 focus:ring-sky-200"
                    maxlength="2000"
                ></textarea>
                <p data-comment-error="body" class="mt-2 hidden text-sm text-red-600"></p>
            </div>

            <div class="flex justify-end">
                <button
                    type="submit"
                    data-comment-submit
                    class="inline-flex justify-center rounded-md bg-sky-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-sky-700 disabled:cursor-not-allowed disabled:opacity-60"
                >
                    Add comment
                </button>
            </div>
        </form>

        <div data-comments-error class="mt-5 hidden rounded-md border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700"></div>
        <p data-comments-loading class="mt-5 text-sm text-slate-600">Loading comments...</p>
        <p data-comments-empty class="mt-5 hidden text-sm text-slate-600">No comments yet.</p>

        <div data-comments-list class="mt-5 space-y-3"></div>

        <button
            type="button"
            data-load-more-comments
            class="mt-5 hidden rounded-md border border-slate-300 bg-white px-4 py-2 text-sm font-medium text-slate-700 shadow-sm hover:bg-slate-50 disabled:cursor-not-allowed disabled:opacity-60"
        >
            Load more
        </button>
    </section>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const manager = document.querySelector('[data-issue-tags]');

            if (!manager) {
                return;
            }

            const attachedList = manager.querySelector('[data-attached-tags]');
            const availableList = manager.querySelector('[data-available-tags]');
            const attachedEmpty = manager.querySelector('[data-empty-attached]');
            const availableEmpty = manager.querySelector('[data-empty-available]');
            const errorBox = manager.querySelector('[data-tag-error]');
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

            const showError = (message) => {
                errorBox.textContent = message;
                errorBox.classList.remove('hidden');
            };

            const clearError = () => {
                errorBox.textContent = '';
                errorBox.classList.add('hidden');
            };

            const toggleEmptyStates = () => {
                const attachedCount = attachedList.querySelectorAll('[data-attached-tag]').length;
                const availableCount = availableList.querySelectorAll('[data-tag-id]').length;

                attachedEmpty.classList.toggle('hidden', attachedCount > 0);
                availableEmpty.classList.toggle('hidden', availableCount > 0);
            };

            const tagColor = (tag) => tag.color || '#64748b';

            const createAttachedTag = (tag, detachUrl) => {
                const wrapper = document.createElement('span');
                wrapper.dataset.attachedTag = '';
                wrapper.dataset.tagId = tag.id;
                wrapper.className = 'inline-flex items-center gap-2 rounded-full bg-slate-100 px-3 py-1.5 text-sm font-medium text-slate-700';

                const swatch = document.createElement('span');
                swatch.className = 'h-2.5 w-2.5 rounded-full';
                swatch.style.backgroundColor = tagColor(tag);

                const name = document.createElement('span');
                name.textContent = tag.name;

                const button = document.createElement('button');
                button.type = 'button';
                button.dataset.tagAction = 'detach';
                button.dataset.url = detachUrl;
                button.dataset.tagName = tag.name;
                button.dataset.tagColor = tag.color || '';
                button.className = 'ml-1 rounded-full px-1 text-slate-500 hover:bg-slate-200 hover:text-slate-900';
                button.setAttribute('aria-label', `Remove ${tag.name} tag`);
                button.textContent = String.fromCharCode(215);

                wrapper.append(swatch, name, button);

                return wrapper;
            };

            const createAvailableTag = (tag, attachUrl) => {
                const button = document.createElement('button');
                button.type = 'button';
                button.dataset.tagId = tag.id;
                button.dataset.tagAction = 'attach';
                button.dataset.url = attachUrl;
                button.dataset.tagName = tag.name;
                button.dataset.tagColor = tag.color || '';
                button.className = 'inline-flex items-center gap-2 rounded-full border border-slate-200 bg-white px-3 py-1.5 text-sm font-medium text-slate-700 hover:bg-slate-50';
                button.setAttribute('aria-label', `Attach ${tag.name} tag`);

                const swatch = document.createElement('span');
                swatch.className = 'h-2.5 w-2.5 rounded-full';
                swatch.style.backgroundColor = tagColor(tag);

                const name = document.createElement('span');
                name.textContent = tag.name;

                const action = document.createElement('span');
                action.className = 'text-sky-700';
                action.textContent = 'Add';

                button.append(swatch, name, action);

                return button;
            };

            manager.addEventListener('click', async (event) => {
                const button = event.target.closest('[data-tag-action]');

                if (!button || !manager.contains(button)) {
                    return;
                }

                clearError();
                button.disabled = true;
                button.classList.add('opacity-60');

                const action = button.dataset.tagAction;
                const method = action === 'attach' ? 'POST' : 'DELETE';

                try {
                    const response = await fetch(button.dataset.url, {
                        method,
                        headers: {
                            'Accept': 'application/json',
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken,
                            'X-Requested-With': 'XMLHttpRequest',
                        },
                    });

                    const data = await response.json();

                    if (!response.ok) {
                        const message = data.message || Object.values(data.errors || {}).flat().join(' ') || 'Unable to update tags.';
                        throw new Error(message);
                    }

                    const tag = data.tag;

                    if (action === 'attach') {
                        attachedList.appendChild(createAttachedTag(tag, button.dataset.url));
                        button.remove();
                    } else {
                        availableList.appendChild(createAvailableTag(tag, button.dataset.url));
                        button.closest('[data-attached-tag]').remove();
                    }

                    toggleEmptyStates();
                } catch (error) {
                    showError(error.message);
                    button.disabled = false;
                    button.classList.remove('opacity-60');
                }
            });
        });
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const manager = document.querySelector('[data-issue-members]');

            if (!manager) {
                return;
            }

            const assignedList = manager.querySelector('[data-assigned-members]');
            const availableList = manager.querySelector('[data-available-members]');
            const assignedEmpty = manager.querySelector('[data-empty-assigned-members]');
            const availableEmpty = manager.querySelector('[data-empty-available-members]');
            const errorBox = manager.querySelector('[data-member-error]');
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

            const showError = (message) => {
                errorBox.textContent = message;
                errorBox.classList.remove('hidden');
            };

            const clearError = () => {
                errorBox.textContent = '';
                errorBox.classList.add('hidden');
            };

            const toggleEmptyStates = () => {
                const assignedCount = assignedList.querySelectorAll('[data-assigned-member]').length;
                const availableCount = availableList.querySelectorAll('[data-member-action="attach"]').length;

                assignedEmpty.classList.toggle('hidden', assignedCount > 0);
                availableEmpty.classList.toggle('hidden', availableCount > 0);
            };

            const createAssignedMember = (user) => {
                const wrapper = document.createElement('div');
                wrapper.dataset.assignedMember = '';
                wrapper.dataset.userId = user.id;
                wrapper.className = 'flex flex-col gap-3 rounded-md bg-slate-100 px-3 py-2 sm:flex-row sm:items-center sm:justify-between';

                const details = document.createElement('div');

                const name = document.createElement('p');
                name.className = 'text-sm font-medium text-slate-800';
                name.textContent = user.name;

                const email = document.createElement('p');
                email.className = 'text-xs text-slate-500';
                email.textContent = user.email;

                const button = document.createElement('button');
                button.type = 'button';
                button.dataset.memberAction = 'detach';
                button.dataset.url = user.detach_url;
                button.dataset.attachUrl = user.attach_url;
                button.dataset.userName = user.name;
                button.dataset.userEmail = user.email;
                button.className = 'inline-flex items-center justify-center rounded-md border border-slate-300 bg-white px-3 py-1.5 text-xs font-medium text-slate-700 hover:bg-slate-50 disabled:cursor-not-allowed disabled:opacity-60';
                button.setAttribute('aria-label', `Remove ${user.name} from issue`);
                button.textContent = 'Remove';

                details.append(name, email);
                wrapper.append(details, button);

                return wrapper;
            };

            const createAvailableMember = (user) => {
                const button = document.createElement('button');
                button.type = 'button';
                button.dataset.memberAction = 'attach';
                button.dataset.userId = user.id;
                button.dataset.url = user.attach_url;
                button.dataset.detachUrl = user.detach_url;
                button.dataset.userName = user.name;
                button.dataset.userEmail = user.email;
                button.className = 'flex flex-col gap-1 rounded-md border border-slate-200 bg-white px-3 py-2 text-left hover:bg-slate-50 disabled:cursor-not-allowed disabled:opacity-60 sm:flex-row sm:items-center sm:justify-between';
                button.setAttribute('aria-label', `Assign ${user.name} to issue`);

                const details = document.createElement('span');

                const name = document.createElement('span');
                name.className = 'block text-sm font-medium text-slate-800';
                name.textContent = user.name;

                const email = document.createElement('span');
                email.className = 'block text-xs text-slate-500';
                email.textContent = user.email;

                const action = document.createElement('span');
                action.className = 'text-sm font-medium text-sky-700';
                action.textContent = 'Add';

                details.append(name, email);
                button.append(details, action);

                return button;
            };

            manager.addEventListener('click', async (event) => {
                const button = event.target.closest('[data-member-action]');

                if (!button || !manager.contains(button)) {
                    return;
                }

                clearError();
                button.disabled = true;

                const action = button.dataset.memberAction;
                const method = action === 'attach' ? 'POST' : 'DELETE';

                try {
                    const response = await fetch(button.dataset.url, {
                        method,
                        headers: {
                            'Accept': 'application/json',
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken,
                            'X-Requested-With': 'XMLHttpRequest',
                        },
                    });

                    const data = await response.json();

                    if (!response.ok) {
                        const message = data.message || Object.values(data.errors || {}).flat().join(' ') || 'Unable to update members.';
                        throw new Error(message);
                    }

                    const user = data.user;

                    if (action === 'attach') {
                        assignedList.appendChild(createAssignedMember(user));
                        button.remove();
                    } else {
                        availableList.appendChild(createAvailableMember(user));
                        button.closest('[data-assigned-member]').remove();
                    }

                    toggleEmptyStates();
                } catch (error) {
                    showError(error.message);
                    button.disabled = false;
                }
            });
        });
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const comments = document.querySelector('[data-comments]');

            if (!comments) {
                return;
            }

            const form = comments.querySelector('[data-comment-form]');
            const submitButton = comments.querySelector('[data-comment-submit]');
            const list = comments.querySelector('[data-comments-list]');
            const loading = comments.querySelector('[data-comments-loading]');
            const empty = comments.querySelector('[data-comments-empty]');
            const loadMoreButton = comments.querySelector('[data-load-more-comments]');
            const errorBox = comments.querySelector('[data-comments-error]');
            const countValue = comments.querySelector('[data-comments-count]');
            const countLabel = comments.querySelector('[data-comments-label]');
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            let nextPageUrl = null;
            let isLoading = false;

            const pluralizeComment = (count) => count === 1 ? 'comment' : 'comments';

            const setCount = (count) => {
                countValue.textContent = String(count);
                countLabel.textContent = pluralizeComment(count);
            };

            const incrementCount = () => {
                setCount(Number.parseInt(countValue.textContent, 10) + 1);
            };

            const showGeneralError = (message) => {
                errorBox.textContent = message;
                errorBox.classList.remove('hidden');
            };

            const clearGeneralError = () => {
                errorBox.textContent = '';
                errorBox.classList.add('hidden');
            };

            const setFieldError = (field, message) => {
                const error = comments.querySelector(`[data-comment-error="${field}"]`);

                if (!error) {
                    return;
                }

                error.textContent = message;
                error.classList.remove('hidden');
            };

            const clearFieldErrors = () => {
                comments.querySelectorAll('[data-comment-error]').forEach((error) => {
                    error.textContent = '';
                    error.classList.add('hidden');
                });
            };

            const toggleEmptyState = () => {
                empty.classList.toggle('hidden', list.children.length > 0 || !loading.classList.contains('hidden'));
            };

            const createCommentElement = (comment) => {
                const article = document.createElement('article');
                article.className = 'rounded-lg border border-slate-200 bg-slate-50 p-4';
                article.dataset.commentId = comment.id;

                const header = document.createElement('div');
                header.className = 'flex flex-col gap-1 sm:flex-row sm:items-center sm:justify-between';

                const author = document.createElement('p');
                author.className = 'text-sm font-semibold text-slate-950';
                author.textContent = comment.author_name;

                const created = document.createElement('time');
                created.className = 'text-xs text-slate-500';
                created.dateTime = comment.created_at || '';
                created.textContent = comment.created_at_human || '';

                const body = document.createElement('p');
                body.className = 'mt-3 whitespace-pre-line text-sm leading-6 text-slate-700';
                body.textContent = comment.body;

                header.append(author, created);
                article.append(header, body);

                return article;
            };

            const setLoadMoreState = () => {
                loadMoreButton.classList.toggle('hidden', !nextPageUrl);
            };

            const loadComments = async (url, append = true) => {
                if (isLoading) {
                    return;
                }

                isLoading = true;
                clearGeneralError();
                loading.classList.remove('hidden');
                loadMoreButton.disabled = true;

                try {
                    const response = await fetch(url, {
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest',
                        },
                    });

                    const data = await response.json();

                    if (!response.ok) {
                        throw new Error(data.message || 'Unable to load comments.');
                    }

                    if (!append) {
                        list.replaceChildren();
                    }

                    data.data.forEach((comment) => {
                        list.appendChild(createCommentElement(comment));
                    });

                    nextPageUrl = data.links.next;
                    setCount(data.meta.total);
                } catch (error) {
                    showGeneralError(error.message);
                } finally {
                    isLoading = false;
                    loading.classList.add('hidden');
                    loadMoreButton.disabled = false;
                    setLoadMoreState();
                    toggleEmptyState();
                }
            };

            form.addEventListener('submit', async (event) => {
                event.preventDefault();

                clearGeneralError();
                clearFieldErrors();
                submitButton.disabled = true;

                const formData = new FormData(form);

                try {
                    const response = await fetch(comments.dataset.storeUrl, {
                        method: 'POST',
                        headers: {
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': csrfToken,
                            'X-Requested-With': 'XMLHttpRequest',
                        },
                        body: formData,
                    });

                    const data = await response.json();

                    if (response.status === 422) {
                        Object.entries(data.errors || {}).forEach(([field, messages]) => {
                            setFieldError(field, messages[0]);
                        });

                        return;
                    }

                    if (!response.ok) {
                        throw new Error(data.message || 'Unable to add comment.');
                    }

                    list.prepend(createCommentElement(data.comment));
                    form.reset();
                    incrementCount();
                    toggleEmptyState();
                } catch (error) {
                    showGeneralError(error.message);
                } finally {
                    submitButton.disabled = false;
                }
            });

            loadMoreButton.addEventListener('click', () => {
                if (nextPageUrl) {
                    loadComments(nextPageUrl);
                }
            });

            loadComments(comments.dataset.indexUrl, false);
        });
    </script>
@endpush
