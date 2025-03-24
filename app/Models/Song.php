<?php

namespace App\Models;

use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Lumen\Auth\Authorizable;

class Song extends Model implements AuthenticatableContract, AuthorizableContract
{
    use Authenticatable, Authorizable, hasFactory;

    protected $fillable = ['album_id', 'name', 'artist', 'duration'];


    public function playlists(){
        return $this->belongsToMany(Playlist::class, 'playlist_songs');
    }

    public function album(){
        return $this->belongsTo(Album::class);
    }
    
}
