<x-layout doctitle="Manage Avatar">
    <div class="container container--narroe py-md-5">
        <h2 class="text-center mb-3">Upload a New Avatar</h2>
        {{-- enctype="multipart/form-data" for laravel to attach the file correctly --}}
        <form action="/manage-avatar" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="mb-3">
                <input type="file" name="avatar" required>
                @error('avatar')
                    <p class="alert small alert-danger shadow-sm">{{$message}}</p>
                @enderror
            </div>
            <button class="btn btn-primary">Save</button>
        </form>
    </div>
</x-layout>
