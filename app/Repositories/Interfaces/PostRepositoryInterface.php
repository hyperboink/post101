<?php

namespace App\Repositories\Interfaces;

interface PostRepositoryInterface{

	public function all();

	public function allByUser(int $id);

	public function paginate(int $limit, int $pageNum);

	public function paginateByUserId(int $pageNum, int $limit, int $userId);

}