<?php

use App\Http\Controllers\CommentController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\IssueAssignmentController;
use App\Http\Controllers\IssueController;
use App\Http\Controllers\IssueTagController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\TagController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/projects');

Route::get('login', [AuthenticatedSessionController::class, 'create'])->name('login');
Route::post('login', [AuthenticatedSessionController::class, 'store']);
Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])->middleware('auth')->name('logout');
Route::get('register', [RegisteredUserController::class, 'create'])->name('register');
Route::post('register', [RegisteredUserController::class, 'store']);

Route::resource('projects', ProjectController::class)
    ->only(['create', 'store', 'edit', 'update', 'destroy'])
    ->middleware('auth');
Route::resource('projects', ProjectController::class)->only(['index', 'show']);
Route::resource('issues', IssueController::class);

Route::get('tags', [TagController::class, 'index'])->name('tags.index');
Route::post('tags', [TagController::class, 'store'])->name('tags.store');

Route::post('issues/{issue}/tags/{tag}', [IssueTagController::class, 'store'])->name('issues.tags.attach');
Route::delete('issues/{issue}/tags/{tag}', [IssueTagController::class, 'destroy'])->name('issues.tags.detach');

Route::post('issues/{issue}/members/{user}', [IssueAssignmentController::class, 'store'])->name('issues.members.attach');
Route::delete('issues/{issue}/members/{user}', [IssueAssignmentController::class, 'destroy'])->name('issues.members.detach');

Route::get('issues/{issue}/comments', [CommentController::class, 'index'])->name('issues.comments.index');
Route::post('issues/{issue}/comments', [CommentController::class, 'store'])->name('issues.comments.store');
