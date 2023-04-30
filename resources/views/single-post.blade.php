<x-layout :doctitle="$post->title">
    <div class="container py-md-5 container--narrow">
        <div class="d-flex justify-content-between">
          <h2>{{$post->title}}</h2>
          {{-- this code points to the PostPolicy.update function to dertermine if the delete and update icon is
          viewable by the logged in user. --}}
          @can('update', $post)
            <span class="pt-2">
                <a href="/post/{{$post->id}}/edit" class="text-primary mr-2" data-toggle="tooltip" data-placement="top" title="Edit"><i class="fas fa-edit"></i></a>
                <form class="delete-post-form d-inline" action="/post/{{$post->id}}" method="POST">
                    @csrf
                    @method('DELETE')
                    <button class="delete-post-button text-danger" data-toggle="tooltip" data-placement="top" title="Delete"><i class="fas fa-trash"></i></button>
                </form>
            </span>
          @endcan
        </div>

        <p class="text-muted small mb-4">
          <a href="/profile/{{$post->user->username}}"><img class="avatar-tiny" src="{{$post->user->avatar}}" /></a>
          {{-- $post->post, the "post" here relates to the foerign key user_id so that you can retrieve the username value of the user id in the post model --}}
          Posted by <a href="/profile/{{$post->user->username}}">{{$post->user->username}}</a> on {{$post->created_at->format('n/j/Y')}}
        </p>

        <div class="body-content">
            {!!$post->body!!}
        </div>
      </div>
</x-layout>
