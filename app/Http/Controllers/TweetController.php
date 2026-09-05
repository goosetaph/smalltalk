<?php

namespace App\Http\Controllers;

use App\Models\Tweet;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;

class TweetController extends Controller
{
    use AuthorizesRequests;

    public function index()
    {
        return view('dashboard', [
            'tweets' => Tweet::latest()->with('user')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'content' => ['required', 'string', 'max:255'],
        ]);

        Tweet::create([
            'user_id' => auth()->id(),
            'content' => $request->content,
        ]);

        session()->flash('success', 'Successfully created a tweet');

        return redirect()->route('dashboard');
    }

    public function show(Tweet $tweet)
    {
        $tweet->load(['user', 'comments.user']);

        return view('tweet.show', compact('tweet'));
    }

    public function edit(Tweet $tweet)
    {
        $this->authorize('update', $tweet);

        return view('tweet.edit', compact('tweet'));
    }

    public function update(Request $request, Tweet $tweet)
    {
        $this->authorize('update', $tweet);

        $request->validate([
            'content' => ['required', 'string', 'max:255'],
        ]);

        $tweet->update([
            'content' => $request->content,
        ]);

        session()->flash('success', 'Successfully updated the tweet');

        return redirect()->route('dashboard');
    }

    public function destroy(Tweet $tweet)
    {
        $this->authorize('delete', $tweet);

        $tweet->delete();

        session()->flash('danger', 'Successfully deleted the tweet');

        return redirect()->route('dashboard');
    }
}
