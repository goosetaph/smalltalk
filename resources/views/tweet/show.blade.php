<x-app-layout>
    <div class="py-12">
        <div class="mx-auto max-w-7xl sm:px-6 lg:p-8 space-y-4">
            <div class="card bg-base-100">
                <div class="card-body">
                    <h3><b>{{ $tweet->user->name }}</b></h3>
                    <p>{{ $tweet->content }}</p>
                </div>
                <div class="card-actions p-2">
                    @can('update', $tweet)
                        <a class="btn btn-warning btn-sm" href="{{ route('tweet.edit', $tweet->id) }}">Edit</a>
                    @endcan
                    @can('delete', $tweet)
                        <form action="{{ route('tweet.destroy', $tweet->id) }}" method="post">
                            @csrf
                            @method('DELETE')
                            <input type="submit" class="btn btn-sm btn-error" value="Delete">
                        </form>
                    @endcan
                </div>
            </div>
            <div class="card mb-2 bg-base-100">
                <div class="card-body">
                    <div class="card-title">Comment</div>
                    <form action="{{ route('comment.store', $tweet) }}" method="post" class="form-control">
                        @csrf
                        <textarea name="message" rows="3" class="textarea textarea-bordered mb-2 @error('message') textarea-error @enderror" placeholder="Add a comment...">{{ old('message') }}</textarea>
                        @error('message')
                            <span class="text-error">{{ $message }}</span>
                        @enderror
                        <div class="card-actions">
                            <input type="submit" value="comment" class="btn btn-secondary">
                        </div>
                    </form>
                </div>
            </div>

            @foreach ($tweet->comments as $comment)
                <div class="card mb-2 bg-base-100">
                    <div class="card-body">
                        <h3><b>{{ $comment->user->name }}</b></h3>
                        <p>{{ $comment->message }}</p>
                    </div>
                    <div class="card-actions p-2">
                        @can('update', $comment)
                            <a class="btn btn-warning btn-sm" href="{{ route('comment.edit', [$tweet, $comment]) }}">Edit</a>
                        @endcan
                        @can('delete', $comment)
                            <form action="{{ route('comment.destroy', [$tweet, $comment]) }}" method="post">
                                @csrf
                                @method('DELETE')
                                <input type="submit" class="btn btn-sm btn-error" value="Delete">
                            </form>
                        @endcan
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</x-app-layout>
