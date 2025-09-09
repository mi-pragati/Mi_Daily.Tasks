<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Post;
use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class BlogFeatureTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_register()
    {
        $response = $this->post('/register', [
            'name' => 'Test User',
            'email' => 'testuser@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $response->assertRedirect('/login');

        $this->assertDatabaseHas('users', [
            'email' => 'testuser@example.com',
        ]);
    }

    public function test_authenticated_user_can_create_post_with_media()
    {
        Storage::fake('public');

        $user = User::factory()->create();
        $category = Category::factory()->create();

        $this->actingAs($user);

        $response = $this->post('/posts', [
            'title' => 'Test Post Title',
            'content' => 'Test post content.',
            'category_id' => $category->id,
            'media' => UploadedFile::fake()->image('test-image.jpg'),
        ]);

        $response->assertRedirect(route('posts.index'));

        $this->assertDatabaseHas('posts', [
            'title' => 'Test Post Title',
            'content' => 'Test post content.',
            'category_id' => $category->id,
            'user_id' => $user->id,
        ]);

        $post = Post::where('title', 'Test Post Title')->first();
        Storage::disk('public')->assertExists($post->media_path);
    }

    public function test_guest_cannot_create_post()
    {
        $category = Category::factory()->create();

        $response = $this->post('/posts', [
            'title' => 'Unauthorized Post',
            'content' => 'Should not be allowed',
            'category_id' => $category->id,
        ]);

        $response->assertRedirect('/login');

        $this->assertDatabaseMissing('posts', [
            'title' => 'Unauthorized Post',
        ]);
    }
}
