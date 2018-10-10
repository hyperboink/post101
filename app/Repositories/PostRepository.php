<?php

namespace App\Repositories;

use App\Repositories\Interfaces\PostRepositoryInterface;
use App\Post;

class PostRepository implements PostRepositoryInterface{

	private $post;

	public function __construct(Post $post){
		$this->post = $post;
	}

	public function all(){
		return $this->post
			->with('user', 'comments', 'comments.user', 'likes', 'likes.user')
			->orderBy('created_at', 'DESC')
			->get();
	}

	public function allByUser(int $id){
		return $this->post
			->where('user_id', $id)
			->with('user', 'comments', 'comments.user', 'likes', 'likes.user')
			->orderBy('created_at', 'DESC')
			->get();
	}

	public function paginate(int $limit = null, int $pageNum = 1){
		return $this->post
			->with('user', 'comments', 'comments.user', 'likes', 'likes.user')
			->orderBy('created_at', 'DESC')
			->paginate($limit, ['*'], 'page', $pageNum);
	}

	public function paginateByUserId(int $limit, int $pageNum, int $userId){
		$wee =  $this->post
			->where('user_id', $userId)
			->with(['user', 'comments' => function($query) use ($limit){
				//return $query->take(3);
			}, 'comments.user', 'likes', 'likes.user']);

		return $wee->orderBy('created_at', 'DESC')
			->paginate($limit, ['*'], 'page', $pageNum);
	}

}