<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTagRequest;
use App\Models\Tag;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class TagController extends Controller
{
    public function index(): View
    {
        return view('tags.index', [
            'tags' => Tag::query()
                ->withCount('issues')
                ->orderBy('name')
                ->get(),
        ]);
    }

    public function store(StoreTagRequest $request): RedirectResponse
    {
        Tag::create($request->validated());

        return redirect()
            ->route('tags.index')
            ->with('success', 'Tag created successfully.');
    }
}
