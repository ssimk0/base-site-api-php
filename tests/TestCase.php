<?php

namespace Tests;

use App\Models\User;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Facades\Auth;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    protected function loginUser($admin = false, $canEdit = false) {
        $user = User::factory()->create(["is_admin" => $admin, "can_edit" => $canEdit]);

        return Auth::login($user);
    }
}
