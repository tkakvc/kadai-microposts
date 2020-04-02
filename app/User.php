<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];
    
    public function microposts()
    {
        return $this->hasMany(Micropost::class);
    }
    
    public function followings()
    {
        return $this->belongsToMany(User::class, 'user_follow', 'user_id', 'follow_id')->withTimestamps();
    }

    public function followers()
    {
        return $this->belongsToMany(User::class, 'user_follow', 'follow_id', 'user_id')->withTimestamps();
    }
    
    public function follow($userId)
    {
        // 既にフォローしているかの確認
        $exist = $this->is_following($userId);
        // 相手が自分自身ではないかの確認
        $its_me = $this->id == $userId;
    
        if ($exist || $its_me) {
            // 既にフォローしていれば何もしない
            return false;
        } else {
            // 未フォローであればフォローする
            $this->followings()->attach($userId);
            return true;
        }
    }
    
    public function unfollow($userId)
    {
        // 既にフォローしているかの確認
        $exist = $this->is_following($userId);
        // 相手が自分自身かどうかの確認
        $its_me = $this->id == $userId;
    
        if ($exist && !$its_me) {
            // 既にフォローしていればフォローを外す
            $this->followings()->detach($userId);
            return true;
        } else {
            // 未フォローであれば何もしない
            return false;
        }
    }
    
    
    public function is_following($userId)
    {
        return $this->followings()->where('follow_id', $userId)->exists();
    }
    
    public function feed_microposts()
    {
        $follow_user_ids = $this->followings()->pluck('users.id')->toArray();
        $follow_user_ids[] = $this->id;
        return Micropost::whereIn('user_id', $follow_user_ids);
    }
     
     
     
     
    // public function favorites()
    // {
    //     return $this->belongsTo(User::class);
    //}
    public function favorites()
    {
        return $this->belongsToMany(Micropost::class, 'favorites', 'user_id', 'micropost_id')->withTimestamps();
    }
    
    public function favorite($micropostId)
    {
        // 既にお気に入り追加しているかの確認
        $exist_favorite = $this->is_favorite($micropostId);
    
        if ($exist_favorite ) {
            // 既にお気に入り追加していれば何もしない
            return false;
        } else {
            // お気に入り追加していなければ追加する
            $this->favorites()->attach($micropostId);
            return true;
        }
    }
        
        public function unfavorite($micropostId)
        {
        // 既にお気に入り追加しているかの確認
            $exist_my_micropost = $this->is_favorite($micropostId);
            
        
            if ($exist_my_micropost) {
                // 既にお気に入り追加していれば外す
                $this->favorites()->detach($micropostId);
                return true;
            } else {
                // お気に入り追加していなければ何もしない
                return false;
            }
        }
        
        public function is_favorite($micropostId)
        {
            return $this->favorites()->where('micropost_id', $micropostId)->exists();
        }
        public function feed_favorites()
    {
        $my_favorite_ids = $this->favorite()->pluck('microposts.id')->toArray();
        $my_favorite_ids[] = $this->id;
        return Micropost::whereIn('micropost_id', $my_favorite_ids);
    }
    
}

