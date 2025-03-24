<?php

namespace App\Models;

use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Lumen\Auth\Authorizable;

class Album extends Model implements AuthenticatableContract, AuthorizableContract
{
    use Authenticatable, Authorizable, hasFactory;

    protected $fillable = [ 'name', 'artist'];

    public function songs(){
        return $this->hasMany(Song::class);
    }
    
    
}
