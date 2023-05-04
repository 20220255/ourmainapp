{{-- x-profile tag calls the profile.blade.php under the resources/views/components folder --}}
<x-profile :sharedData='$sharedData' doctitle="{{$sharedData['username']}}'s Profile">
    @include('profile-posts-only')
</x-profile>
