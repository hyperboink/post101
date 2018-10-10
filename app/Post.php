<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Auth;
use Carbon\Carbon;

class Post extends Model
{
    protected $fillable = ['user_id', 'title', 'content'];

    protected $guarded = [];

    protected $dates = ['created_at', 'updated_at'];

    public function user(){
    	return $this->belongsTo(User::class);
    }

    public function comments(){
    	return $this->hasMany(Comment::class);
    }

    public function likes(){
    	return $this->hasMany(Like::class);
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

    public function countComments(){
        return count($this->comments);
    }

    public function likedOnly(){
        return $this->likes->where('is_liked', 1);
    }

    public function countLikes(){
        return count($this->likedOnly());
    }

    public function authLike(){
        foreach($this->likedOnly() as $like){
            if($like->user_id == Auth::user()->id){
                return true;
            }
        }
    }
}
