<?php

namespace App\Base;

use App\Base\Interfaces\BaseRepositoryInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Pagination\LengthAwarePaginator;

abstract class BaseRepository implements BaseRepositoryInterface{

	protected $model;

	public function __construct(Model $model){
		$this->model = $model;
	}

	public function all($columns = array('*'), string $orderBy = 'id', string $sortBy = 'asc'){
		return $this->model->orderBy($orderBy, $sortBy)->get($columns);
	}

	public function find(int $id){
		return $this->model->find($id);
	}

	public function findBy(array $data){
		return $this->model->where($data)->all();
	}

	public function findOneBy(array $data){
		return $this->model->where($data)->first();
	}

	public function findOneByOrFail(array $data){
		return $this->model->where($data)->firstOrFail();
	}

	public function paginateArrayResults(array $data, int $perPage = 50)
	{
		$page = request()->get('page', 1);
		$offset = ($page * $perPage) - $perPage;
		return new LengthAwarePaginator(
			array_slice($data, $offset, $perPage, false),
			count($data),
			$perPage,
			$page,
			[
				'path' => request()->url(),
				'query' => request()->query()
			]
		);
	}

	public function create(array $attributes){
		return $this->model->create($attributes);
	}

	public function update(array $attributes, int $id) : bool {
		return $this->model->find($id)->update($attributes);
	}

	public function delete(int $id){
		return $this->model->find($id)->delete();
	}

}