<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Http\Middleware\ValidateCsrfToken;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutMiddleware(ValidateCsrfToken::class);
    }

    public function test_user_can_register(): void
    {
        $response = $this->post(route('register'), [
            'name' => 'Project Owner',
            'email' => 'owner@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $response->assertRedirect(route('projects.index'));
        $this->assertAuthenticated();
        $this->assertDatabaseHas('users', [
            'name' => 'Project Owner',
            'email' => 'owner@example.com',
        ]);
        $this->assertTrue(Hash::check('password', User::where('email', 'owner@example.com')->first()->password));
    }

    public function test_user_can_login(): void
    {
        $user = User::factory()->create([
            'email' => 'owner@example.com',
            'password' => Hash::make('password'),
        ]);

        $response = $this->post(route('login'), [
            'email' => 'owner@example.com',
            'password' => 'password',
        ]);

        $response->assertRedirect(route('projects.index'));
        $this->assertAuthenticatedAs($user);
    }

    public function test_failed_login_shows_validation_error(): void
    {
        User::factory()->create([
            'email' => 'owner@example.com',
            'password' => Hash::make('password'),
        ]);

        $response = $this->from(route('login'))->post(route('login'), [
            'email' => 'owner@example.com',
            'password' => 'wrong-password',
        ]);

        $response
            ->assertRedirect(route('login'))
            ->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_user_can_logout(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('logout'));

        $response->assertRedirect(route('projects.index'));
        $this->assertGuest();
    }
}
