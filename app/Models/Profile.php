<?php

namespace App\Models;

use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Lumen\Auth\Authorizable;

class Profile extends Model implements AuthenticatableContract, AuthorizableContract
{
    use Authenticatable, Authorizable, hasFactory;

    protected $fillable = ['user_id', 'phone', 'address', 'dob'];

    public function user(){
        return $this->belongsTo(User::class);
    }
    
}
