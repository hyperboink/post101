<?php
namespace App\Base\Interfaces;

interface BaseRepositoryInterface{

	//public function all(){}

	public function find(){}

	public function findBy(){}

	public function findOneBy(){}

	public function findOneByOrFail(){}

	public function paginateArrayResults(){}

	public function create(){}

	public function update(){}

	public function delete(){}

}