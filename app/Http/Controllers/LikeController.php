<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Notifications\LikedComment;
use App\Like;

class LikeController extends Controller
{
    protected $like;

    public function __construct(Like $like){
        $this->like = $like;
    }

    public function index(Request $request){

        $data = [
            'post_id' => $request->post_id,
            'user_id' => $request->user_id
        ];

    	$like = $this->like::updateOrCreate($data, [
			'is_liked' => $request->is_liked
		]);

        $liker = $this->like->where($data)->whereColumn('created_at', 'updated_at')->first();

        if($liker){
            $liker->post->user->notify(new LikedComment(auth()->user(), $liker));
        }

    	return $like;
    }
}
