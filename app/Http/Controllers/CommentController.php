<?php

namespace App\Http\Controllers;

use App\Notifications\NewComment;
use Illuminate\Http\Request;
use App\Repositories\CommentRepository;
use App\Comment;
use App\Post;
use Redirect;
use Validator;

class CommentController extends Controller
{
    protected $comment;
    protected $commentRepo;
    protected $commentLimit = 2;
    protected $startPage = 1;

    public function __construct(CommentRepository $commentRepository, Comment $comment){
        $this->commentRepo = $commentRepository;
        $this->comment = $comment;
    }

    public function store(Request $request){

        $user = auth()->user();

        $rules = [
            'content' => 'required'
        ];

        $validator = Validator::make($request->except('_token'), $rules);

        if($validator->fails()){
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ]);
        }else{
            $this->comment->create($request->except('_token'));

            $comment = $this->comment->where('post_id', $request->post_id)->first();

            if($user->id != $comment->post->user->id){
                $comment->post->user->notify(new NewComment($user, $comment));
            }

            return Comment::with('user', 'post')
                ->orderBy('created_at', 'desc')
                ->first();
        }

       
    }

    public function edit(Request $request){
        return $this->comment->findOrFail($request->id);
    }

    public function update(Request $request){
        $comment = $this->comment->findOrFail($request->id);
        $comment->update($request->except('_token'));

        return $comment;
    }

    public function delete(Request $request){
        $comment = $this->comment->findOrFail($request->id);
        $comment->delete();

        return response()->json([
            'deleted' => $comment,
            'updated' => $this->load($request)
        ]);
    }

    public function load(Request $request){

        $comments = $this->commentRepo->paginate($this->commentLimit, $request->post_id, $request->page);

        return $comments;

    }

}
