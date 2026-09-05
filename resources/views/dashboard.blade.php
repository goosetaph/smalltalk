<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <div class="bg-base-100 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-white-900">
                    <form action="{{ route('tweet.store') }}" class="form-control" method="post">
                        @csrf
                        <textarea name="content" cols="30" rows="3" class="textarea textarea-bordered mb-2 @error('content') textarea-error @enderror" placeholder="Write some text..."></textarea>
                        @error('content')
                            <span class="text-error">{{ $message }}</span>
                        @enderror
                        <input type="submit" value="Send" class="btn btn-primary">
                    </form>
                </div>
            </div>
            <div class="mt-2 flex flex-col space-y-2">
                @foreach ($tweets as $tweet)
                    <div class="card bg-base-100">
                        <div class="card-body">
                            <h3><b>{{ $tweet->user->name }}</b></h3>
                            <p>{{ $tweet->content }}</p>
                        </div>
                        <div class="card-actions p-2">
                        <a href="{{ route('tweet.show', $tweet) }}" class="btn btn-info btn-sm">Comment</a>
                            <a class="btn btn-warning btn-sm" href="{{ route('tweet.edit', $tweet->id) }}">Edit</a>
                            <form action="{{ route('tweet.destroy', $tweet->id) }}" method="post">
                                @csrf
                                @method('DELETE')
                                <input type="submit" class="btn btn-sm btn-error" value="Delete">
                            </form>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</x-app-layout>
