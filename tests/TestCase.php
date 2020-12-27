<?php

namespace Tests;

use App\Models\User;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Facades\Auth;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    protected function loginUser($admin = false) {
        $user = User::factory()->create(["is_admin" => $admin]);

        return Auth::login($user);
    }
}
