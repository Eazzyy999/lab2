<?php
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
Route::get('/qb/all', [App\Http\Controllers\QueryBuilderController::class, 'all']);
Route::get('/qb/filter', [App\Http\Controllers\QueryBuilderController::class, 'filter']);
Route::get('/qb/selectedColumns', [App\Http\Controllers\QueryBuilderController::class, 'selectedColumns']);
Route::get('/qb/paginated', [App\Http\Controllers\QueryBuilderController::class, 'paginated']);
Route::get('/qb/aggregates', [App\Http\Controllers\QueryBuilderController::class, 'aggregates']);
Route::get('/qb/joinInner', [App\Http\Controllers\QueryBuilderController::class, 'joinInner']);
Route::get('/qb/joinLeft', [App\Http\Controllers\QueryBuilderController::class, 'joinLeft']);
Route::get('/qb/joinRight', [App\Http\Controllers\QueryBuilderController::class, 'joinRight']);
Route::get('/qb/insertUpdateDelete', [App\Http\Controllers\QueryBuilderController::class, 'insertUpdateDelete']);
Route::get('/eq/index', [App\Http\Controllers\EloquentController::class, 'index']);
Route::get('/eq/show/{id}', [App\Http\Controllers\EloquentController::class, 'show']);
Route::get('/eq/bigData', [App\Http\Controllers\EloquentController::class, 'bigData']);
Route::get('/eq/eager', [App\Http\Controllers\RelationshipController::class, 'eagerLoading']);
Route::get('/eq/filters', [App\Http\Controllers\RelationshipController::class, 'filters']);
Route::get('/eq/aggregates', [App\Http\Controllers\RelationshipController::class, 'aggregates']);
Route::get('/raw-demo', function () {
    DB::statement('CREATE TABLE IF NOT EXISTS log_entries (id INT AUTO_INCREMENT PRIMARY KEY, message VARCHAR(255), created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP)');
    DB::unprepared("INSERT INTO log_entries (message) VALUES ('Init log')");

    DB::insert('insert into posts (user_id, title, content) values (?, ?, ?)', [1, 'Raw Title', 'Raw Content']);
    $insertedPostId = DB::getPdo()->lastInsertId();

    $affected = DB::update('update posts set title = ? where id = ?', ['Updated Raw Title', $insertedPostId]);
    $deleted = DB::delete('delete from posts where id = ?', [$insertedPostId]);

    $users = DB::select('select * from users where id > ? limit 5', [0]);

    DB::transaction(function () {
        DB::insert('insert into posts (user_id, title, content) values (?, ?, ?)', [1, 'Tx Post', 'Tx Content']);
        $id = DB::getPdo()->lastInsertId();
        DB::insert('insert into comments (post_id, user_id, body) values (?, ?, ?)', [$id, 1, 'Tx Comment']);
    });

    return response()->json([
        'message' => 'Raw SQL queries executed successfully',
        'users' => $users
    ]);
});