<?php

return [

	/*
    |--------------------------------------------------------------------------
    | Posts Config
    |--------------------------------------------------------------------------
    |
    | Here you may define all configuaration of the "posts" in your application.
    | 
 	|
    */

	'posts' => [
		'paginate' => [
			'limit' => 7,
			'start' => 1
		]
	],

	/*
    |--------------------------------------------------------------------------
    | Comments Config
    |--------------------------------------------------------------------------
    |
    | Here you may define all configuaration of the "comments" in your application.
    | 
 	|
    */
	'comments' => [
		'paginate' => [
			'limit' => 10,
			'start' => 1
		]
		
	]
];