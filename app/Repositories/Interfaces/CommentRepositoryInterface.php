<?php

namespace App\Repositories\Interfaces;


interface CommentRepositoryInterface{

	public function paginate(int $limit, int $pageNum, int $postId);

}