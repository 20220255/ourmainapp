{{-- x-profile tag calls the profile.blade.php under the resources/views/components folder --}}
<x-profile :sharedData="$sharedData" doctitle="{{$sharedData['username']}}'s Followers">
    @include('profile-followers-only')
</x-profile>
