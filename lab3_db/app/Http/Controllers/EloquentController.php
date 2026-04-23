<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;

class EloquentController extends Controller
{
    public function index() {
        return response()->json(Post::where('is_published', true)->take(10)->get());
    }

    public function store(Request $request) {
        $data = $request->validate([
            'user_id' => 'required|exists:users,id',
            'title' => 'required|string|max:150',
            'content' => 'required'
        ]);
        
        $post = Post::create($data);
        return response()->json($post, 201);
    }

    public function show($id) {
        return response()->json(Post::findOrFail($id));
    }

    public function update(Request $request, $id) {
        $post = Post::findOrFail($id);
        $post->update($request->only(['title', 'content']));
        return response()->json($post);
    }

    public function destroy($id) {
        Post::destroy($id);
        return response()->json(['message' => 'Deleted']);
    }

    public function bigData() {
        $startMemory = memory_get_usage();
        
        $posts = Post::lazy();
        foreach ($posts as $post) {
            $id = $post->id;
        }

        return response()->json([
            'peak_memory_mb' => memory_get_peak_usage() / 1024 / 1024
        ]);
    }
}