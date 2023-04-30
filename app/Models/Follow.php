<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Follow extends Model
{
    use HasFactory;

    // This will get the other details from User model or user table based
    // on the column of user_id from the follows
    public function userDoingTheFollowing() {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function userBeingFollowed() {
        return $this->belongsTo(User::class, 'followeduser');
    }
}
