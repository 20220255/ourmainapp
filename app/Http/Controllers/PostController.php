<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Support\Str;
use Illuminate\Http\Request;

class PostController extends Controller
{

    public function search($term){
        $posts = Post::search($term)->get();
        $posts->load('user:id,username,avatar');
        return $posts;
    }

    public function showCreateForm() {
        return view('create-post');
    }

    public function storeNewPost(Request $request) {
        $incomingFields = $request->validate([
            'title' => 'required',
            'body' => 'required'
        ]);

        // used for malicious tags - strip_tags
        $incomingFields['title'] = strip_tags($incomingFields['title']);
        $incomingFields['body'] = strip_tags($incomingFields['body']);
        // auth() will call the id of the current logged in user from the the user table.
        $incomingFields['user_id'] = auth()->id();

        // this will save whatever was entered in the $request fields and into the posts table. This was
        // made possible by creating a Post.php model class and where it connects to the actual posts table
        // in our database.
        $newPost = Post::create($incomingFields);

        return redirect("/post/{$newPost->id}")->with('success', 'Successfully created new post');
    }
    // type hinting
    public function viewSinglePost(Post $post) {

        // body here is the property of the Post model class, where you can access the body of the blog post.
        $post['body'] = strip_tags(Str::markdown($post->body), '<p><ul><li><strong><em><h3><br><hr>');

        return view('/single-post', ['post' => $post]);
    }

    public function delete(Post $post) {
        $post->delete();
        return redirect('/profile/' . auth()->user()->username)->with('success', 'successfully deleted');
    }

    public function showEditForm(Post $post) {
        return view('edit-post', ['post' => $post]);
    }

    public function actuallyUpdate(Post $post, Request $request) {
        $incomingFields = $request->validate([
            'title' => 'required',
            'body' => 'required'
        ]);

        $incomingFields['title'] = strip_tags($incomingFields['title']);
        $incomingFields['body'] = strip_tags($incomingFields['body']);

        $post->update($incomingFields);

        // back() wil just bring you back to the previous url that you came from
        return back()->with('success','successfully updated');

    }


}
