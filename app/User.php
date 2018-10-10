<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\Hash;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'username', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    public function posts(){
        return $this->hasMany(Post::class);
    }

    public function comments(){
        return $this->hasMany(Comment::class);
    }

    public function likes(){
        return $this->hasMany(Like::class);
    }

    /*public function setPasswordAttribute($value){
        return $this->attributes = Hash::make($value);
    }*/

    public function followers(){
        return $this->belongsToMany(self::class, 'followers', 'followed_id', 'user_id')
            ->withTimestamps();
    }

    public function followed(){
        return $this->belongsToMany(self::class, 'followers', 'user_id', 'followed_id')
            ->withTimestamps();
    }

    public function follow($userId){
        $this->followed()->attach($userId);
        return $this;
    }

    public function unfollow($userId){
        $this->followed()->detach($userId);
        return $this;
    }

    public function isFollowing($userId){
        return (boolean) $this->followed()->where('followed_id', $userId)->first();
    }
}
