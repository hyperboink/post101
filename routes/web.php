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
/*
Route::get('/', function () {
    return view('welcome');
});*/

Route::get('/', [
	'uses' => 'PostController@index',
	'as' => 'blog.home'
]);


Route::group(['middleware' => 'auth'], function(){

	Route::get('users', [
		'uses' => 'UserController@users',
		'as' => 'users'
	]);

	Route::post('user/follow/{user}', [
		'uses' => 'UserController@follow',
		'as' => 'follow'
	]);

	Route::delete('user/unfollow/{user}', [
		'uses' => 'UserController@unfollow',
		'as' => 'unfollow'
	]);

	//Profile
	Route::get('/profile', [
		'uses' => 'UserController@index',
		'as' => 'user.profile'
	]);

	Route::get('/profile/settings', [
		'uses' => 'UserController@settings',
		'as' => 'user.settings'
	]);

	//lazyload
	Route::get('/post/paginate', 'PostController@load');
	
	Route::get('/post/paginate/profile', 'UserController@load');

	// POSTS
	Route::get('/post', [
		'uses' => 'PostController@create',
		'as' => 'post.create'
	]);

	Route::get('/post/show/{id}', [
		'uses' => 'PostController@show',
		'as' => 'post.single'
	]);

	Route::get('/post/{id}', [
		'uses' => 'PostController@edit',
		'as' => 'post.edit'
	]);

	Route::post('/post', [
		'uses' => 'PostController@store',
		'as' => 'post.save'
	]);

	Route::put('/post/update/{id}', [
		'uses' => 'PostController@update',
		'as' => 'post.update'
	]);

	Route::delete('/post/delete', [
		'uses' => 'PostController@delete',
		'as' => 'post.delete'
	]);

	// Other Profiles
	Route::get('/profile/{username}', 'UserController@otherUserProfile');



	//COMMENTS
	Route::post('/post/comments', [
		'uses' => 'CommentController@store',
		'as' => 'comment.save'
	]);

	Route::get('/post/comment/{id}', [
		'uses' => 'CommentController@edit',
		'as' => 'comment.edit'
	]);

	Route::put('/post/comment/update/{id}', [
		'uses' => 'CommentController@update',
		'as' => 'comment.update'
	]);

	Route::delete('/post/comment/delete', [
		'uses' => 'CommentController@delete',
		'as' => 'comment.delete'
	]);

	//LIKES
	Route::post('post/like/{postId}', 'LikeController@index');

	//NOTIFICATION
	Route::get('/notifications', 'UserController@notifications');


});

//Route::get('comments/paginate', 'CommentController@load');

Route::get('/test', 'UserController@test')->name('test');


Auth::routes();


/*\DB::listen(function ($query) {
	echo "<pre>";
    var_dump($query->sql);
    echo '</pre>';
});*/