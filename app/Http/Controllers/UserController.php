<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Repositories\PostRepository;
use App\Notifications\UserFollowed;
use App\User;
use Response;
use Carbon\Carbon;
use App\Post;
use Image;

class UserController extends Controller
{
	protected $postRepo;
	protected $startPage = 1;

	public function __construct(PostRepository $postRepository){
		$this->postRepo = $postRepository;
	}

	public function index(){

		$user = auth()->user();
		
		$posts = $this->postRepo->paginateByUserId(config('custom_config.posts.paginate.limit'), $this->startPage, $user->id);

		foreach($posts as $post){

			if(!count($post->likes)){
				$post->auth_liked = false;
			}

			foreach($post->likes as $like){
				$post->auth_liked = $like->user_id === $user->id;
			}

		}

		//return $posts;

		return view('blog/profile', compact('user','posts'));

	}

	public function otherUserProfile($username){

		$user = User::where('username', $username)->first();

		$posts = $this->postRepo->paginateByUserId(config('custom_config.posts.paginate.limit'), 1, $user->id);

		return view('blog/other-profile', compact('posts', 'user'));

	}

	public function settings(){
		$user = User::where('id', auth()->user()->id)->first();
		return view('user.settings', compact('user'));
	}

	public function update(Request $request){
		$user = User::where('id', $request->id)->first();
		$user->update($request->except('_token'));

		return redirect()->back()->with(['message' => 'Account updated!.']);
	}

	public function load(Request $request){

		$posts = $this->postRepo->paginateByUserId(config('custom_config.posts.paginate.limit'), $request->pageNum, auth()->user()->id);

		return $posts;

	}


	// Follow

	public function users(){
		$users = User::where('id', '!=', auth()->user()->id)->get();

		$user = User::find(1);

		$notifications = $user->notifications;

		return view('follow.index', compact('users', 'notifications'));
	}

	public function follow(User $user){

		$auth = auth()->user();

		if($auth->id == $user->id){
			return response()->json([
				'success' => false,
				'message' => 'You can\'t follow yourself.'
			]);
		}

		if(!$auth->isFollowing($user->id)){
			$auth->follow($user->id);
			$user->notify(new UserFollowed($auth));

			return response()->json([
				'isFollowing' => true,
				'success' => true,
				'message' => 'You are now following '.$user->name
			]);
		}

		return response()->json([
			'success' => false,
			'message' => 'You already followed '.$user->name
		]);
	}

	public function unfollow(User $user){

		$auth = auth()->user();

		if($auth->isFollowing($user->id)){
			$auth->unfollow($user->id);
			
			return response()->json([
				'isFollowing' => false,
				'success' => true,
				'message' => 'You are no longer following '.$user->name
			]);
			//return back()->withSuccess('You are no longer following {$user->name}');
		}

		return response()->json([
			'success' => true,
			'message' => 'You are not following {$user->name}'
		]);

	}

	//Notification
	public function notifications(){
		//return auth()->user()->unreadNotifications()->paginate(5);
		return auth()->user()->Notifications()->paginate(5);
	}

	//test
	public function test(Request $request){
		return dd($request->file('weeimage'));
		$img = Image::make($request->file('weeimage'));

    	//return $img;
	}

}
