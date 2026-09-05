<?php

namespace Tests\Feature;

use App\Models\Tweet;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TweetTest extends TestCase
{
    use RefreshDatabase;

    private function tweetFor(User $user, string $content = 'A tweet'): Tweet
    {
        return Tweet::create([
            'user_id' => $user->id,
            'content' => $content,
        ]);
    }

    public function test_dashboard_displays_tweets(): void
    {
        $user = User::factory()->create();
        $this->tweetFor($user, 'Hello from the dashboard');

        $response = $this->actingAs($user)->get('/dashboard');

        $response->assertOk();
        $response->assertSee('Hello from the dashboard');
    }

    public function test_user_can_create_a_tweet(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->post('/tweet', [
                'content' => 'My first tweet',
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('dashboard'));

        $this->assertDatabaseHas('tweets', [
            'user_id' => $user->id,
            'content' => 'My first tweet',
        ]);
    }

    public function test_tweet_content_is_required(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->from('/dashboard')
            ->post('/tweet', [
                'content' => '',
            ]);

        $response->assertSessionHasErrors('content');

        $this->assertDatabaseCount('tweets', 0);
    }

    public function test_tweet_content_may_not_exceed_255_characters(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->from('/dashboard')
            ->post('/tweet', [
                'content' => str_repeat('a', 256),
            ]);

        $response->assertSessionHasErrors('content');

        $this->assertDatabaseCount('tweets', 0);
    }

    public function test_a_tweet_can_be_shown(): void
    {
        $user = User::factory()->create();
        $tweet = $this->tweetFor($user, 'Show me');

        $response = $this->actingAs($user)->get("/tweet/{$tweet->id}/show");

        $response->assertOk();
        $response->assertSee('Show me');
    }

    public function test_owner_can_see_the_edit_form(): void
    {
        $user = User::factory()->create();
        $tweet = $this->tweetFor($user);

        $response = $this->actingAs($user)->get("/tweet/{$tweet->id}/edit");

        $response->assertOk();
    }

    public function test_owner_can_update_their_tweet(): void
    {
        $user = User::factory()->create();
        $tweet = $this->tweetFor($user, 'Before');

        $response = $this
            ->actingAs($user)
            ->put("/tweet/{$tweet->id}/update", [
                'content' => 'After',
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('dashboard'));

        $this->assertSame('After', $tweet->fresh()->content);
    }

    public function test_owner_can_delete_their_tweet(): void
    {
        $user = User::factory()->create();
        $tweet = $this->tweetFor($user);

        $response = $this
            ->actingAs($user)
            ->delete("/tweet/{$tweet->id}/destroy");

        $response->assertRedirect(route('dashboard'));

        $this->assertDatabaseMissing('tweets', ['id' => $tweet->id]);
    }

    public function test_non_owner_can_not_open_the_edit_form_of_another_users_tweet(): void
    {
        $owner = User::factory()->create();
        $other = User::factory()->create();
        $tweet = $this->tweetFor($owner);

        $response = $this->actingAs($other)->get("/tweet/{$tweet->id}/edit");

        $response->assertForbidden();
    }

    public function test_non_owner_can_not_update_another_users_tweet(): void
    {
        $owner = User::factory()->create();
        $other = User::factory()->create();
        $tweet = $this->tweetFor($owner, 'Original');

        $response = $this
            ->actingAs($other)
            ->put("/tweet/{$tweet->id}/update", [
                'content' => 'Hacked',
            ]);

        $response->assertForbidden();

        $this->assertSame('Original', $tweet->fresh()->content);
    }

    public function test_non_owner_can_not_delete_another_users_tweet(): void
    {
        $owner = User::factory()->create();
        $other = User::factory()->create();
        $tweet = $this->tweetFor($owner);

        $response = $this
            ->actingAs($other)
            ->delete("/tweet/{$tweet->id}/destroy");

        $response->assertForbidden();

        $this->assertDatabaseHas('tweets', ['id' => $tweet->id]);
    }

    public function test_guests_can_not_create_a_tweet(): void
    {
        $response = $this->post('/tweet', [
            'content' => 'Anonymous tweet',
        ]);

        $response->assertRedirect('/login');

        $this->assertDatabaseCount('tweets', 0);
    }

    public function test_guests_are_redirected_from_the_dashboard(): void
    {
        $response = $this->get('/dashboard');

        $response->assertRedirect('/login');
    }
}
