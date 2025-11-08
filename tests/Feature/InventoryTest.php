<?php

use App\Models\Inventory;
use App\Models\User;

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->actingAs($this->user);
});

test('guests cannot access inventory index', function () {
    auth()->logout();

    $response = $this->get(route('inventory.index'));
    $response->assertRedirect(route('login'));
});

test('authenticated users can view inventory index', function () {
    Inventory::factory()->count(5)->create();

    $response = $this->get(route('inventory.index'));
    $response->assertSuccessful();
    $response->assertInertia(fn ($page) => $page
        ->component('Inventory/Index')
        ->has('inventories.data', 5)
    );
});

test('users can search inventory items', function () {
    Inventory::factory()->create(['name' => 'Laptop Computer']);
    Inventory::factory()->create(['name' => 'Desktop Computer']);
    Inventory::factory()->create(['name' => 'Mouse']);

    $response = $this->get(route('inventory.index', ['search' => 'Laptop']));
    $response->assertSuccessful();
    $response->assertInertia(fn ($page) => $page
        ->component('Inventory/Index')
        ->has('inventories.data', 1)
        ->where('inventories.data.0.name', 'Laptop Computer')
    );
});

test('users can filter inventory by category', function () {
    Inventory::factory()->create(['category' => 'Electronics']);
    Inventory::factory()->create(['category' => 'Electronics']);
    Inventory::factory()->create(['category' => 'Clothing']);

    $response = $this->get(route('inventory.index', ['category' => 'Electronics']));
    $response->assertSuccessful();
    $response->assertInertia(fn ($page) => $page
        ->component('Inventory/Index')
        ->has('inventories.data', 2)
    );
});

test('authenticated users can view create inventory page', function () {
    $response = $this->get(route('inventory.create'));
    $response->assertSuccessful();
    $response->assertInertia(fn ($page) => $page
        ->component('Inventory/Create')
    );
});

test('users can create inventory items', function () {
    $data = [
        'name' => 'Test Item',
        'sku' => 'TEST-123',
        'quantity' => 10,
        'price' => 99.99,
        'category' => 'Electronics',
        'location' => 'Warehouse A',
        'description' => 'Test description',
        'is_active' => true,
    ];

    $response = $this->post(route('inventory.store'), $data);
    $response->assertRedirect(route('inventory.index'));
    $response->assertSessionHas('success');

    $this->assertDatabaseHas('inventory_items', [
        'name' => 'Test Item',
        'sku' => 'TEST-123',
        'quantity' => 10,
        'price' => 99.99,
    ]);
});

test('users cannot create inventory with duplicate sku', function () {
    Inventory::factory()->create(['sku' => 'DUPLICATE-123']);

    $data = [
        'name' => 'Test Item',
        'sku' => 'DUPLICATE-123',
        'quantity' => 10,
        'price' => 99.99,
    ];

    $response = $this->post(route('inventory.store'), $data);
    $response->assertSessionHasErrors('sku');
});

test('inventory creation requires name, sku, quantity, and price', function () {
    $response = $this->post(route('inventory.store'), []);
    $response->assertSessionHasErrors(['name', 'sku', 'quantity', 'price']);
});

test('authenticated users can view edit inventory page', function () {
    $inventory = Inventory::factory()->create();

    $response = $this->get(route('inventory.edit', $inventory));
    $response->assertSuccessful();
    $response->assertInertia(fn ($page) => $page
        ->component('Inventory/Edit')
        ->where('inventory.id', $inventory->id)
    );
});

test('users can update inventory items', function () {
    $inventory = Inventory::factory()->create([
        'name' => 'Old Name',
        'quantity' => 5,
    ]);

    $data = [
        'name' => 'Updated Name',
        'sku' => $inventory->sku,
        'quantity' => 15,
        'price' => $inventory->price,
        'is_active' => true,
    ];

    $response = $this->put(route('inventory.update', $inventory), $data);
    $response->assertRedirect(route('inventory.index'));
    $response->assertSessionHas('success');

    $this->assertDatabaseHas('inventory_items', [
        'id' => $inventory->id,
        'name' => 'Updated Name',
        'quantity' => 15,
    ]);
});

test('users can update inventory with same sku', function () {
    $inventory = Inventory::factory()->create(['sku' => 'SAME-SKU']);

    $data = [
        'name' => 'Updated Name',
        'sku' => 'SAME-SKU',
        'quantity' => 10,
        'price' => 99.99,
    ];

    $response = $this->put(route('inventory.update', $inventory), $data);
    $response->assertRedirect(route('inventory.index'));
    $response->assertSessionHas('success');
});

test('users cannot update inventory with duplicate sku from another item', function () {
    $inventory1 = Inventory::factory()->create(['sku' => 'SKU-1']);
    $inventory2 = Inventory::factory()->create(['sku' => 'SKU-2']);

    $data = [
        'name' => 'Updated Name',
        'sku' => 'SKU-2',
        'quantity' => 10,
        'price' => 99.99,
    ];

    $response = $this->put(route('inventory.update', $inventory1), $data);
    $response->assertSessionHasErrors('sku');
});

test('users can delete inventory items', function () {
    $inventory = Inventory::factory()->create();

    $response = $this->delete(route('inventory.destroy', $inventory));
    $response->assertRedirect(route('inventory.index'));
    $response->assertSessionHas('success');

    $this->assertDatabaseMissing('inventory_items', [
        'id' => $inventory->id,
    ]);
});
