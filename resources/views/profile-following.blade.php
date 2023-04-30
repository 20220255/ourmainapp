{{-- x-profiel tag calls the profile.blade.php under the resources/views/components folder --}}
<x-profile :sharedData="$sharedData" doctitle="Who are {{$sharedData['username']}} following">
    <div class="list-group">
        @foreach($followings as $following)
            <a href="/profile/{{$following->userBeingFollowed->username}}" class="list-group-item list-group-item-action">
                <img class="avatar-tiny" src="{{$following->userBeingFollowed->avatar}}" />
                {{$following->userBeingFollowed->username}}
            </a>
        @endforeach
    </div>
</x-profile>
