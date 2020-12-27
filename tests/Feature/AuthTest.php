<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use DatabaseMigrations, WithFaker;

    public function test_register_user()
    {
        $user = User::factory()->makeOne();
        $pass = $this->faker->password(6, 32);

        $response = $this->postJson('/api/v1/auth/register-user', [
            'email' => $user->email,
            'first_name' => $user->first_name,
            'last_name' => $user->last_name,
            'password' => $pass,
            'password_confirmation' => $pass
        ]);

        $response->assertStatus(201)->assertJson([
            'success' => true,
        ]);
    }

    public function test_register_user_with_same_email()
    {
        $user = User::factory()->create();
        $pass = $this->faker->password(6, 32);

        $response = $this->postJson('/api/v1/auth/register-user', [
            'email' => $user->email,
            'first_name' => $user->first_name,
            'last_name' => $user->last_name,
            'password' => $pass,
            'password_confirmation' => $pass
        ]);

        $response->assertStatus(400);
    }

    public function test_login()
    {
        $user = User::factory()->create();

        $response = $this->postJson('/api/v1/auth/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $response->assertStatus(200);

        $response->assertJsonFragment([
            'token_type' => 'bearer'
        ]);
    }

    public function test_forgot_password()
    {
        $user = User::factory()->create();

        $response = $this->postJson('/api/v1/auth/forgot-password', [
            'email' => $user->email,
        ]);

        $response->assertStatus(200)->assertJson([
            'success' => true,
        ]);
    }

    public function test_forgot_password_user_not_found()
    {
        $response = $this->postJson('/api/v1/auth/forgot-password', [
            'email' => $this->faker->email,
        ]);

        $response->assertStatus(404);
    }

    public function test_user_info()
    {
        $user = User::factory()->create();

        $token = Auth::login($user);

        $response = $this->getJson('/api/v1/auth/user', [
            'Authorization' => 'Bearer ' . $token
        ]);

        $response->assertStatus(200)->assertJsonFragment([
            'id' => $user->id,
            'first_name' => $user->first_name,
            'last_name' => $user->last_name,
            'email' => $user->email,
            'is_admin' => false,
        ]);
    }

    public function test_reset_password()
    {
        $user = User::factory()->create();
        $token = Str::random(30);
        DB::table('password_resets')->insert([
            'email' => $user->email,
            'token' => $token
        ]);

        $response = $this->postJson('/api/v1/auth/reset-password/' . $token, [
            'password' => 'test1234',
            'password_confirmation' => 'test1234'
        ]);

        $response->assertStatus(200)->assertJson([
            'success' => true
        ]);
    }

    public function test_reset_password_wrong_token()
    {
        $token = Str::random(30);
        DB::table('password_resets')->insert([
            'email' => $this->faker->email,
            'token' => $token
        ]);

        $response = $this->postJson('/api/v1/auth/reset-password/' . $token, [
            'password' => 'test1234',
            'password_confirmation' => 'test1234'
        ]);

        $response->assertStatus(404);
    }
}
