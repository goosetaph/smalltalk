<?php
namespace App\Http\Controllers;

use App\Models\Tweet;
use Illuminate\Http\Request;
use Illuminate\Foundation\Validation\ValidatesRequests;

class TweetController extends Controller
{
    use ValidatesRequests;

    public function index()
    {
        return view('dashboard', [
            'tweets' => Tweet::latest()->get(),
        ]);
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'content' => ['required'],
        ]);

        Tweet::create([
            'user_id' => auth()->id(),
            'content' => $request->content,
        ]);

        session()->flash('success', 'Successfully created a tweet');

        return redirect()->route('dashboard');
    }

    public function show($tweet)
    {
        return view('tweet.show', [
            'tweet' => Tweet::find($tweet)
        ]);
    }

    public function edit(Tweet $tweet)
    {
        return view('tweet.edit', compact('tweet'));
    }

    public function update(Request $request, Tweet $tweet)
    {
        $this->validate($request, [
            'content' => ['required'],
        ]);

        $tweet->update([
            'content' => $request->content,
        ]);

        session()->flash('success', 'Successfully updated the tweet');

        return redirect()->route('dashboard');
    }

    public function destroy($id)
    {
        $tweet = Tweet::find($id);

        $tweet->delete();

        session()->flash('danger', 'Successfully deleted the tweet');

        return redirect()->route('dashboard');
    }
}
