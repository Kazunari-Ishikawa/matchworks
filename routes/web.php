<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('index');
});
Auth::routes();

// ログイン時のみのルーティング
Route::group(['middleware' => 'auth'], function(){
    // Users
    Route::get('/users/edit', 'UsersController@edit')->name('users.edit');
    Route::post('/users/edit', 'UsersController@update');
    Route::get('/users/password/edit', 'UsersController@editPassword')->name('users.editPassword');
    Route::post('/users/password/edit', 'UsersController@updatePassword');
    Route::get('/withdraw', 'UsersController@showWithdrawForm')->name('withdraw');
    Route::post('/withdraw', 'UsersController@withdraw');
    Route::get('/mypage', 'UsersController@mypage')->name('users.mypage');

    // Works
    Route::get('/works/new', 'WorksController@new')->name('works.new');
    Route::post('/works/new', 'WorksController@create');
    Route::get('/works/{id}/edit', 'WorksController@edit')->name('works.edit');
    Route::post('/works/{id}/edit', 'WorksController@update')->name('works.update');
    Route::post('/works/{id}/delete', 'WorksController@destroy')->name('works.destroy');
    Route::post('/works/{id}/close', 'WorksController@close')->name('works.close');
    Route::post('/works/{id}/apply', 'WorksController@apply')->name('works.apply');
    Route::post('/works/{id}/cancel', 'WorksController@cancel')->name('works.cancel');
    Route::get('/works/registered', 'WorksController@showRegisteredWorks')->name('works.registered');
    Route::get('/works/applied', 'WorksController@showAppliedWorks')->name('works.applied');
    Route::get('/works/closed', 'WorksController@showClosedWorks')->name('works.closed');
    Route::get('/works/bookmarks', 'WorksController@showBookmarksWorks')->name('works.bookmarks');
    Route::get('/api/works/registered', 'WorksController@getRegisteredWorks');
    Route::get('/api/works/commented', 'WorksController@getCommentedWorks');
    Route::get('/api/works/applied', 'WorksController@getAppliedWorks');
    Route::get('/api/works/closed', 'WorksController@getClosedWorks');
    Route::get('/api/works/bookmarks', 'WorksController@getBookmarksWorks');

    // Bookmarks
    Route::post('/api/bookmarks/{id}/add', 'BookmarksController@add');
    Route::post('/api/bookmarks/{id}/delete', 'BookmarksController@delete');

    // Comments
    Route::get('/comments', 'CommentsController@index')->name('comments.index');
    Route::post('/works/{id}/comments/create', 'CommentsController@create')->name('comments.create');
    Route::get('/api/works/{id}/comments/latest', 'CommentsController@getLatestComment');
    Route::post('/api/comments/{id}/delete', 'CommentsController@destroy');

    // Messages
    Route::get('/messages', 'BoardsController@index')->name('messages.index');
    Route::get('/api/boards', 'BoardsController@getBoards');
    Route::get('/api/boards/{id}/messages/latest', 'MessagesController@getLatestMessage');
    Route::get('/messages/{id}', 'BoardsController@show')->name('messages.show');
    Route::get('/api/messages/{id}', 'MessagesController@getMessages');
    Route::post('/api/messages/{id}', 'MessagesController@sendMessage');
    Route::post('/api/messages/{id}/delete', 'MessagesController@deleteMessage');
});

Route::get('/users/{id}', 'UsersController@show')->name('users.show');
Route::get('/works', 'WorksController@index')->name('works.index');
Route::get('/api/works', 'WorksController@getworks');
Route::get('/works/{id}', 'WorksController@show')->name('works.show');
Route::get('/api/works/{id}/comments', 'CommentsController@getComments');
Route::post('/api/works/search', 'WorksController@searchWorks');
