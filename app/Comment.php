<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Comment extends Model
{
	use Notifiable;

    protected $fillable = ['user_id', 'post_id', 'content'];

    protected $dates = ['created_at', 'updated_at'];

    public function user(){
    	return $this->belongsTo(User::class);
    }

    public function post(){
    	return $this->belongsTo(Post::class);
    }

 	public function getCreatedAtAttribute($value){
        return Carbon::parse($value);
    }

    public function getUpdatedAtAttribute($value){
        return Carbon::parse($value);
    }

    public function isEdited(){
    	return $this->created_at != $this->updated_at;
    }

    public function commented($postId){
    	return $this->post()->where('posts_id', $postId)->first();
    }
}
