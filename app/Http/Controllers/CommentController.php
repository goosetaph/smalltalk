<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Tweet;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    use AuthorizesRequests;

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, Tweet $tweet)
    {
        $request->validate([
            'message' => ['required', 'string', 'max:2000'],
        ]);

        Comment::create([
            'tweet_id' => $tweet->id,
            'user_id' => auth()->id(),
            'message' => $request->message,
        ]);

        session()->flash('success', 'Successfully added the comment...');

        return redirect()->back();
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Tweet $tweet, Comment $comment)
    {
        $this->authorize('update', $comment);

        return view('comment.edit', compact('comment'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Tweet $tweet, Comment $comment)
    {
        $this->authorize('update', $comment);

        $request->validate([
            'message' => ['required', 'string', 'max:2000'],
        ]);

        $comment->update([
            'message' => $request->message,
        ]);

        session()->flash('success', 'Successfully updated the comment...');

        return to_route('tweet.show', $tweet);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Tweet $tweet, Comment $comment)
    {
        $this->authorize('delete', $comment);

        $comment->delete();

        session()->flash('danger', 'Successfully deleted the comment...');

        return to_route('tweet.show', $tweet);
    }
}
