{{-- x-profiel tag calls the profile.blade.php under the resources/views/components folder --}}
<x-profile :sharedData="$sharedData" doctitle="Who are {{$sharedData['username']}} following">
    @include('profile-followings-only')
</x-profile>
