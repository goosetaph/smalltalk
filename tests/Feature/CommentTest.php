<?php

namespace Tests\Feature;

use App\Models\Comment;
use App\Models\Tweet;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CommentTest extends TestCase
{
    use RefreshDatabase;

    private function tweetFor(User $user, string $content = 'A tweet'): Tweet
    {
        return Tweet::create([
            'user_id' => $user->id,
            'content' => $content,
        ]);
    }

    private function commentFor(User $user, Tweet $tweet, string $message = 'A comment'): Comment
    {
        return Comment::create([
            'tweet_id' => $tweet->id,
            'user_id' => $user->id,
            'message' => $message,
        ]);
    }

    public function test_user_can_add_a_comment_to_a_tweet(): void
    {
        $author = User::factory()->create();
        $commenter = User::factory()->create();
        $tweet = $this->tweetFor($author);

        $response = $this
            ->actingAs($commenter)
            ->from("/tweet/{$tweet->id}/show")
            ->post("/tweet/{$tweet->id}/comment", [
                'message' => 'Nice tweet!',
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect("/tweet/{$tweet->id}/show");

        $this->assertDatabaseHas('comments', [
            'tweet_id' => $tweet->id,
            'user_id' => $commenter->id,
            'message' => 'Nice tweet!',
        ]);
    }

    public function test_comment_message_is_required(): void
    {
        $user = User::factory()->create();
        $tweet = $this->tweetFor($user);

        $response = $this
            ->actingAs($user)
            ->from("/tweet/{$tweet->id}/show")
            ->post("/tweet/{$tweet->id}/comment", [
                'message' => '',
            ]);

        $response->assertSessionHasErrors('message');

        $this->assertDatabaseCount('comments', 0);
    }

    public function test_comments_are_shown_on_the_tweet_page(): void
    {
        $user = User::factory()->create();
        $tweet = $this->tweetFor($user);
        $this->commentFor($user, $tweet, 'A visible comment');

        $response = $this->actingAs($user)->get("/tweet/{$tweet->id}/show");

        $response->assertOk();
        $response->assertSee('A visible comment');
    }

    public function test_owner_can_update_their_comment(): void
    {
        $user = User::factory()->create();
        $tweet = $this->tweetFor($user);
        $comment = $this->commentFor($user, $tweet, 'Before');

        $response = $this
            ->actingAs($user)
            ->put("/tweet/{$tweet->id}/comment/{$comment->id}", [
                'message' => 'After',
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect("/tweet/{$tweet->id}/show");

        $this->assertSame('After', $comment->fresh()->message);
    }

    public function test_owner_can_delete_their_comment(): void
    {
        $user = User::factory()->create();
        $tweet = $this->tweetFor($user);
        $comment = $this->commentFor($user, $tweet);

        $response = $this
            ->actingAs($user)
            ->delete("/tweet/{$tweet->id}/comment/{$comment->id}");

        $response->assertRedirect("/tweet/{$tweet->id}/show");

        $this->assertDatabaseMissing('comments', ['id' => $comment->id]);
    }

    public function test_non_owner_can_not_open_the_edit_form_of_another_users_comment(): void
    {
        $owner = User::factory()->create();
        $other = User::factory()->create();
        $tweet = $this->tweetFor($owner);
        $comment = $this->commentFor($owner, $tweet);

        $response = $this
            ->actingAs($other)
            ->get("/tweet/{$tweet->id}/comment/{$comment->id}/edit");

        $response->assertForbidden();
    }

    public function test_non_owner_can_not_update_another_users_comment(): void
    {
        $owner = User::factory()->create();
        $other = User::factory()->create();
        $tweet = $this->tweetFor($owner);
        $comment = $this->commentFor($owner, $tweet, 'Original');

        $response = $this
            ->actingAs($other)
            ->put("/tweet/{$tweet->id}/comment/{$comment->id}", [
                'message' => 'Hacked',
            ]);

        $response->assertForbidden();

        $this->assertSame('Original', $comment->fresh()->message);
    }

    public function test_non_owner_can_not_delete_another_users_comment(): void
    {
        $owner = User::factory()->create();
        $other = User::factory()->create();
        $tweet = $this->tweetFor($owner);
        $comment = $this->commentFor($owner, $tweet);

        $response = $this
            ->actingAs($other)
            ->delete("/tweet/{$tweet->id}/comment/{$comment->id}");

        $response->assertForbidden();

        $this->assertDatabaseHas('comments', ['id' => $comment->id]);
    }

    public function test_guests_can_not_comment(): void
    {
        $user = User::factory()->create();
        $tweet = $this->tweetFor($user);

        $response = $this->post("/tweet/{$tweet->id}/comment", [
            'message' => 'Anonymous comment',
        ]);

        $response->assertRedirect('/login');

        $this->assertDatabaseCount('comments', 0);
    }
}
