<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Post;

class RelationshipController extends Controller
{
    public function eagerLoading() {
        $posts = Post::with(['user', 'comments'])->limit(5)->get();
        return response()->json($posts);
    }

    public function filters() {
        $users = User::has('posts')->get();
        return response()->json($users);
    }

    public function aggregates() {
        $users = User::withCount('posts')->get();
        return response()->json($users);
    }
}