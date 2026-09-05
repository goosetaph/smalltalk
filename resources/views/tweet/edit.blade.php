<x-app-layout>
    <div class="py-12">
        <div class="mx-auto max-w-7xl bg-base-100 sm:px-6 lg:p-8">
            <form action="{{ route('tweet.update', $tweet->id) }}" class="form-control" method="post">
                @csrf
                @method('PUT')
                <div class="mb-3">
                    <textarea name="content" cols="30" rows="3" class="textarea textarea-bordered w-full @error('content') textarea-error @enderror">{{ old('content', $tweet->content) }}</textarea>
                @error('content')
                    <span class="text-error">{{ $message }}</span>
                @enderror
                </div>
                <div>
                    <input type="submit" value="Edit" class="btn btn-secondary">
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
