<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Follow;
use Illuminate\Http\Request;

class FollowController extends Controller
{
    public function createFollow(User $user) {

        // you cannot follow yourself
        if ($user->id == auth()->user()->id) {
            return back()->with('failure', 'You cannot follow youorself.');
        }

        // you cannot follow someone you're already folllowing the where clause
        // here is the 2 arrays are an "AND", meaning it's looking for a record
        // where the id of the logged in user and the id of the followed user
        // exists in the same record inside the follows table, by using the count method.
        $existCheck = Follow::where([['user_id', '=', auth()->user()->id], ['followeduser', '=', $user->id]])->count();
        if ($existCheck) {
            return back()->with('failure', 'You are already following that user.');
        }

        $newFollow = new Follow;
        $newFollow->user_id = auth()->user()->id;
        $newFollow->followeduser = $user->id;

        $newFollow->save();

        return back()->with('success', 'User successfully followed.');

    }

    public function removeFollow(User $user) {
        Follow::where([['user_id', '=', auth()->id()], ['followeduser', '=', $user->id]])->delete();
        return back()->with('success', 'User successfully unfollowed.');
    }


}
