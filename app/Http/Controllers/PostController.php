<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Repositories\PostRepository;
use App\Post;
use App\User;
use Validator;

class PostController extends Controller
{
	protected $postRepo;

	public function __construct(PostRepository $postRepository){
		$this->postRepo = $postRepository;
	}

	public function index(){
		//Display all the post
		$user = auth()->user();

		$posts = $this->postRepo->paginate(config('custom_config.posts.paginate.limit'), 1);

		return view('home', compact('posts', 'user'));
	}

	public function create(){
		return view('blog/create');
	}

	public function store(Request $request){

		$rules = [
			'title' => 'required',
			'content' => 'required'
		];

		$validator = Validator::make($request->except('_token'), $rules);

		if($validator->fails()){
			return response()->json([
				'success' => false,
				'errors' => $validator->errors()
			]);
		}else{
			$post = Post::create($request->except('_token'));

			return $post->with('user')->orderBy('created_at', 'DESC')->first();
		}

		

	}

	public function show($id){

		$user = auth()->user();

		$post = Post::findOrFail($id);

		return view('blog/single-post', compact('post', 'user'));

	}

	public function edit($id){

		$post = Post::findOrFail($id);

		return $post;

	}

	public function update(Request $request, $id){

		$rules = [
			'title' => 'required',
			'content' => 'required'
		];

		$validator = Validator::make($request->except('_token'), $rules);

		if($validator->fails()){
			return response()->json([
				'success' => false,
				'errors' => $validator->errors()
			]);
		}else{
			$post = Post::findOrFail($id);
			$post->update($request->except('_token'));

			return $post;
		}

		
	}

	public function delete(Request $request){

		$post = Post::findOrFail($request->id);
		$post->delete();

	}

	public function load(Request $request){

		$posts = $this->postRepo->paginate(config('custom_config.posts.paginate.limit'), $request->pageNum);

		return $posts;

	}
	
}
