<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Repositories\PostRepository;
use App\Notifications\UserFollowed;
use App\User;
use Response;
use Carbon\Carbon;
use App\Post;

class HomeController extends Controller
{
    protected $postRepo;
    protected $startPage = 1;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(PostRepository $postRepository)
    {
        $this->middleware('auth');
        $this->postRepo = $postRepository;
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
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

    //Notification
    public function notifications(){
        return auth()->user()->Notifications()->paginate(5);
    }
}
