<?php

use App\Http\Controllers\IssueController;
use App\Http\Controllers\IssueTagController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\TagController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/projects');

Route::resource('projects', ProjectController::class);
Route::resource('issues', IssueController::class);

Route::get('tags', [TagController::class, 'index'])->name('tags.index');
Route::post('tags', [TagController::class, 'store'])->name('tags.store');

Route::post('issues/{issue}/tags/{tag}', [IssueTagController::class, 'store'])->name('issues.tags.attach');
Route::delete('issues/{issue}/tags/{tag}', [IssueTagController::class, 'destroy'])->name('issues.tags.detach');
