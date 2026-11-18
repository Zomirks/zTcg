<?php

use App\Models\User;

test('admin user is admin', function () {
    $admin = User::factory()->admin()->make();

    expect($admin->isAdmin())->toBeTrue();
});

test('user is not admin', function () {
    $user = User::factory()->make();

    expect($user->isAdmin())->toBeFalse();
});
