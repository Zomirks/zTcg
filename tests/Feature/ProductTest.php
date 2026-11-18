<?php

use App\Models\Product;
use App\Models\User;

test('user can\'t create a product', function () {
    $user = User::factory()->create();
    $payload = Product::factory()->make()->toArray();

    $this->actingAs($user)->post(route('products.store'), $payload)->assertForbidden();
    $this->assertDatabaseCount('products', 0);
});

test('user can\'t open create route product', function () {
    $user = User::factory()->create();

    $this->actingAs($user)->get(route('products.create'))->assertForbidden();
});

test('user gets 403 even with an empty payload', function () {
    $user = User::factory()->create();
    $payload = [];

    $this->actingAs($user)->post(route('products.store'), $payload)->assertForbidden();
    $this->assertDatabaseCount('products', 0);
});

test('admin can create a product', function () {
    $admin = User::factory()->admin()->create();
    $payload = Product::factory()->make()->toArray();

    $this->actingAs($admin)->post(route('products.store'), $payload)->assertRedirect(route('products.index'));
    $this->assertDatabaseCount('products', 1);
});
