<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\User;
use App\Models\Follow;
use Illuminate\Http\Request;
use App\Events\OurExampleEvent;
use Illuminate\Support\Facades\Cache;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\View;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{
    //
    public function register(Request $request) {
        $incomingFields = $request->validate([
            //for validation
            //username is the value taken from the name attribute of the form element in blade file
            'username' => ['required', 'min:3', 'max:20', Rule::unique('users', 'username')],
            'email' => ['required', 'email', Rule::unique('users', 'email')],
            'password' => ['required', 'min:8', 'confirmed']
        ]);

        $incomingFields['password'] = bcrypt($incomingFields['password']);

        // This is mass assigning, you need to specify in the User model (User.php) each
        // fields in order for the create function to work.
        $user =  User::create($incomingFields);
        auth()->login($user);


        return redirect('/')->with('success', 'Thank you for creating an account.');
    }

    public function loginApi(Request $request) {
        $incomingFields = $request->validate([
            'username' => 'required',
            'password' => 'required'
        ]);

        if (auth()->attempt($incomingFields)) {
            $user = User::where('username', $incomingFields['username'])->first();
            $token = $user->createToken('ourapptoken')->plainTextToken;
            return $token;
        }
        return 'Sorry';

    }

    public function login(Request $request) {
        // for validation
        $incomingFields = $request->validate([
            // name attribute in the form element in the blade file
            'loginusername' => 'required',
            'loginpassword' => 'required'
        ]);

        // auth() is a hlelper function that is globally available. This will return
        // and object and then call a method called "attempt" using "->". This will compare
        // email and password woth the hased value in the database.
        if (auth()->attempt(['username' => $incomingFields['loginusername'], 'password' => $incomingFields['loginpassword']])) {
            // the below code creates a session value that will be stored in a cookie
            // cookie is sent from the browser to the server in every single request automatically.
            // this means that the server can trust the browser since this was created when the
            // user was authenticated during the time he or she logged in.
            $request->session()->regenerate();
            event(new OurExampleEvent(['username' => auth()->user()->username, 'action' => 'login']));
            return redirect('/')->with('success','You have successfully logged in.');
        } else {
            return redirect('/')->with('failure', 'Invalid login');
        }

    }

    public function logout() {
        event(new OurExampleEvent(['username' => auth()->user()->username, 'action' => 'logout']));
        auth()->logout();
        return redirect('/')->with('success', 'You are now logged out.');
    }

    public function showCorrectHomepage(Request $request) {
     if (auth()->check()) {
        return  view('homepage-feed', ['posts' => auth()->user()->feedPosts()->latest()->paginate(3)]);
     } else {
        $postCount = Cache::remember('postCount', 20, function() {
            //sleep(5);
            return Post::count();
        });
        return view('homepage', ['postCount' => $postCount]);
     };
    }

    private function getSharedData($user) {
        $currentlyFollowing = 0;
        if (auth()->check()) {
            $currentlyFollowing = Follow::where([['user_id', '=', auth()->id()], ['followeduser', '=', $user->id]])->count();
        }
        View::share('sharedData', ['currentlyFollowing' => $currentlyFollowing, 'avatar' => $user->avatar,  'username' => $user->username, 'postCount' => $user->posts()->count(), 'followerCount' => $user->followers()->count(), 'followingCount' => $user->followingTheseUsers()->count()]);
    }

    public function profile(User $user) {
        $this->getSharedData($user);
        // see User model and check posts method where User has one-to-many posts relationship
        // $thePosts = $user->posts()->get();
        // return $thePosts;
        return view('profile-posts', ['posts' => $user->posts()->latest()->get()]);
    }

    public function profileRaw(User $user) {
        return response()->json(['theHTML' => view('profile-posts-only', ['posts' => $user->posts()->latest()->get()])->render(), 'docTitle' => $user->username . "'s Profile."]);
    }


    public function profileFollowers(User $user) {
        $this->getSharedData($user);
        return view('profile-followers', ['followers' => $user->followers()->latest()->get()]);
    }

    public function profileFollowersRaw(User $user) {
        return response()->json(['theHTML' => view('profile-followers-only', ['followers' => $user->followers()->latest()->get()])->render(), 'docTitle' => $user->username . "'s Followers."]);
    }


    public function profileFollowing(User $user) {
        $this->getSharedData($user);
        // see User model and check posts method where User has one-to-many posts relationship
        // $thePosts = $user->posts()->get();
        // return $thePosts;
        return view('profile-following', ['followings' => $user->followingTheseUsers()->latest()->get()]);
    }

    public function profileFollowingRaw(User $user) {
        // see User model and check posts method where User has one-to-many posts relationship
        // $thePosts = $user->posts()->get();
        // return $thePosts;
        return response()->json(['theHTML' => view('profile-followings-only', ['followings' => $user->followingTheseUsers()->latest()->get()])->render(), 'docTitle' => "Users whom " . $user->username . " are following."]);
    }


    public function showAvatarForm() {
        return view('avatar-form');
    }

    public function storeAvatar(Request $request) {
        // validate first the incoming data request
        // use pipe symbol to add validations i.e. shold be image, reuired, and
        // max of 3MB.
        $request->validate([
            'avatar' => 'required|image|max:5000'
        ]);

        $user = auth()->user();
        $filename = $user->id . '-' . uniqid() . '.jpg';

        // This will resize the image that you downloaded and you can define its
        // size using fit method and enforce the type of image by using the
        // encode method.
        $imgData = Image::make($request->file('avatar'))->fit(120)->encode('jpg');

        // this will store the image that was resized in the local server directory
        Storage::put('public/avatars/' . $filename, $imgData);

        $oldAvatar = $user->avatar;

        // this will automatically save it to the database since $user was taken from
        // the global auth() function where it takes in the user() function connected
        // to the model User.
        $user->avatar = $filename;
        $user->save();

        if ($oldAvatar != "/fallback-avatar.jpg") {
            Storage::delete(str_replace("/storage/", "public/", $oldAvatar));
        }

        return back()->with('success', 'Congatrs on the new avatar.');

    }

}
