<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;

class QueryBuilderController extends Controller
{
    // 1. all
    public function all() {
        return response()->json(DB::table('users')->get());
    }

    // 2. filter (додано всі типи where)
    public function filter() {
        $data = DB::table('posts')
            ->where('is_published', true)
            ->orWhere('title', 'like', '%test%')
            ->whereIn('user_id', [1, 2, 3]) // Вибірка за масивом ID
            ->whereBetween('id', [1, 10])   // Діапазон
            ->whereNull('deleted_at')       // Ті, що не видалені (якщо є softDeletes)
            ->get();
        return response()->json($data);
    }

    // 3. selectedColumns
    public function selectedColumns() {
        return response()->json(DB::table('users')->select('name', 'email as contact')->get());
    }

    // 4. paginated (виправлено на 10)
    public function paginated() {
        return response()->json(DB::table('posts')->paginate(10));
    }

    // 5. aggregates (додано count, sum, avg, min, max)
    public function aggregates() {
        // Оскільки в соцмережі немає "ціни", для прикладу агрегуємо id (або можна likes_count, якщо додаси таку колонку)
        return response()->json([
            'count' => DB::table('posts')->count(),
            'sum_id' => DB::table('posts')->sum('id'),
            'avg_id' => DB::table('posts')->avg('id'),
            'min_id' => DB::table('posts')->min('id'),
            'max_id' => DB::table('posts')->max('id')
        ]);
    }

    // 6. joinInner (3 таблиці)
    public function joinInner() {
        $data = DB::table('comments')
            ->join('posts', 'comments.post_id', '=', 'posts.id')
            ->join('users', 'comments.user_id', '=', 'users.id')
            ->select('users.name', 'posts.title', 'comments.body')
            ->get();
        return response()->json($data);
    }

    // 7. joinLeft (отримання сутностей з порожніми зв'язками)
    public function joinLeft() {
        $data = DB::table('users')
            ->leftJoin('profiles', 'users.id', '=', 'profiles.user_id')
            ->select('users.name', 'profiles.bio')
            ->get();
        return response()->json($data);
    }

    // 8. joinRight (звіт по категоріях/типах - в нашому випадку по постах)
    public function joinRight() {
        $data = DB::table('comments')
            ->rightJoin('posts', 'comments.post_id', '=', 'posts.id')
            ->select('posts.title', DB::raw('COUNT(comments.id) as comments_count'))
            ->groupBy('posts.id', 'posts.title')
            ->get();
        return response()->json($data);
    }

    // 9. insertUpdateDelete
    public function insertUpdateDelete() {
        // Демонстрація Insert
        $newPostId = DB::table('posts')->insertGetId([
            'user_id' => 1,
            'title' => 'Query Builder Title',
            'content' => 'Query Builder Content',
            'created_at' => now(),
            'updated_at' => now()
        ]);

        // Демонстрація Update
        DB::table('posts')
            ->where('id', $newPostId)
            ->update(['title' => 'Updated Query Builder Title']);

        // Демонстрація Delete
        DB::table('posts')
            ->where('id', $newPostId)
            ->delete();

        return response()->json([
            'message' => "Successfully inserted, updated, and deleted post ID: {$newPostId}"
        ]);
    }
}