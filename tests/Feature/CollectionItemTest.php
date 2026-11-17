<?php

use App\Models\CollectionItem;
use App\Models\Product;
use App\Models\User;
use Inertia\Testing\AssertableInertia as Assert;

test('guest is redirected to login', function (string $method, string $route) {
    $this->{$method}(route($route))
        ->assertRedirect(route('login'));
})->with([
    'index' => ['get', 'collection-items.index'],
    'create' => ['get', 'collection-items.create'],
    'store' => ['post', 'collection-items.store'],
]);

test('user only sees their own collection items', function () {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();
    CollectionItem::factory()->for($user)->count(2)->create();
    CollectionItem::factory()->for($otherUser)->create();

    $this->actingAs($user)
        ->get(route('collection-items.index'))
        ->assertInertia(fn (Assert $page) => $page->component('collection-items/index')->has('items', 2)->where('items', fn ($items) => $items->pluck('user_id')->unique()->all() === [$user->id]));
});

test('stored item ignores the posted user_id', function () {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();
    $product = Product::factory()->create();

    $payload = [
        'product_id' => $product->id,
        'quantity' => 1,
        'purchase_price' => 60,
        'product_condition' => 'sealed',
        'user_id' => $otherUser->id,
    ];

    $this->actingAs($user)
        ->post(route('collection-items.store'), $payload)
        ->assertSessionHasNoErrors()
        ->assertRedirect(route('collection-items.index'));

    $this->assertDatabaseHas('collection_items', [
        'product_id' => $product->id,
        'user_id' => $user->id,
    ]);

    $this->assertDatabaseMissing('collection_items', ['user_id' => $otherUser->id]);
});

test('store rejects invalid data', function (string $field, mixed $value) {
    $user = User::factory()->create();
    $product = Product::factory()->create();

    $payload = [
        'product_id' => $product->id,
        'quantity' => 1,
        'purchase_price' => 60,
        'product_condition' => 'sealed',
    ];

    $payload[$field] = $value;

    $this->actingAs($user)
        ->post(route('collection-items.store'), $payload)
        ->assertSessionHasErrors($field);

    $this->assertDatabaseCount('collection_items', 0);
})->with([
    'quantity at 0' => ['quantity', 0],
    'negative price' => ['purchase_price', -1],
    'unknown product' => ['product_id', 999999],
    'invalid condition' => ['product_condition', 'broken'],
]);

test('user can update their item', function () {
    $owner = User::factory()->create();
    $item = CollectionItem::factory()->for($owner)->create(['purchase_price' => 60]);

    $this->actingAs($owner)
        ->put(route('collection-items.update', $item), ['purchase_price' => 45])
        ->assertSessionHasNoErrors()
        ->assertRedirect(route('collection-items.index'));

    $this->assertDatabaseHas('collection_items', [
        'id' => $item->id,
        'purchase_price' => 45,
    ]);
});

test('user cannot update another user item', function () {
    $owner = User::factory()->create();
    $otherUser = User::factory()->create();
    $item = CollectionItem::factory()->for($owner)->create(['purchase_price' => 60]);

    $this->actingAs($otherUser)
        ->put(route('collection-items.update', $item), ['purchase_price' => 45])
        ->assertForbidden();

    $this->assertDatabaseHas('collection_items', [
        'id' => $item->id,
        'purchase_price' => 60,
    ]);
});

test('only owner can delete their item', function () {
    $owner = User::factory()->create();
    $otherUser = User::factory()->create();
    $item = CollectionItem::factory()->for($owner)->create();

    $this->actingAs($otherUser)
        ->delete(route('collection-items.destroy', $item))
        ->assertForbidden();

    $this->assertModelExists($item);

    $this->actingAs($owner)
        ->delete(route('collection-items.destroy', $item))
        ->assertSessionHasNoErrors()
        ->assertRedirect(route('collection-items.index'));

    $this->assertModelMissing($item);
});
