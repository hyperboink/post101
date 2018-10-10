<?php

namespace App\Repositories;

use App\Repositories\Interfaces\CommentRepositoryInterface;
use App\Comment;

class CommentRepository implements CommentRepositoryInterface{

	protected $comment;

	public function __construct(Comment $comment){
		$this->comment = $comment;
	}

	public function paginate(int $limit = null, $postId = null, $pageNum = 1){
		return $this->comment
			->with('user')
			->when($postId, function($query) use ($postId){
				$query->where('post_id', $postId);
			})->orderBy('created_at', 'DESC')
			->paginate($limit, ['*'], 'page', $pageNum);
	}

}

