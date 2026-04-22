<?php

use App\Models\Product;
use App\Models\Supplier;
use App\Models\Type;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

uses(RefreshDatabase::class);

function fakePng(string $name): UploadedFile
{
    return UploadedFile::fake()->createWithContent(
        $name,
        base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mP8/x8AAwMCAO+WnM0AAAAASUVORK5CYII=')
    );
}

it('creates and updates types and blocks deletion when a product uses the type', function () {
    $response = $this->postJson('/api/types', [
        'name' => 'Eletronicos',
        'description' => 'Itens de tecnologia.',
    ]);

    $response
        ->assertCreated()
        ->assertJsonPath('name', 'Eletronicos');

    $typeId = $response->json('id');

    $this->putJson("/api/types/{$typeId}", [
        'name' => 'Eletronicos e Acessorios',
        'description' => 'Linha principal de tecnologia.',
    ])
        ->assertOk()
        ->assertJsonPath('name', 'Eletronicos e Acessorios');

    Product::create([
        'name' => 'Monitor UltraWide',
        'description' => 'Tela para escritorio.',
        'quantity' => 4,
        'price' => 1999.90,
        'type_id' => $typeId,
    ]);

    $this->deleteJson("/api/types/{$typeId}")
        ->assertStatus(422)
        ->assertJsonPath('message', 'Nao e possivel remover um tipo que possui produtos vinculados.');
});

it('creates and updates suppliers with profile photo upload', function () {
    Storage::fake('public');

    $createResponse = $this->post('/api/suppliers', [
        'name' => 'Tech Supply',
        'contact_name' => 'Marina Costa',
        'email' => 'contato@techsupply.test',
        'phone' => '(11) 99999-9999',
        'document' => '12.345.678/0001-99',
        'notes' => 'Fornecedor premium.',
        'is_active' => '1',
        'profile_photo' => fakePng('profile.png'),
    ], [
        'Accept' => 'application/json',
    ]);

    $createResponse
        ->assertCreated()
        ->assertJsonPath('name', 'Tech Supply');

    $supplier = Supplier::firstOrFail();

    expect($supplier->profile_photo_path)->not->toBeNull();
    Storage::disk('public')->assertExists($supplier->profile_photo_path);

    $oldPhotoPath = $supplier->profile_photo_path;

    $this->post("/api/suppliers/{$supplier->id}", [
        '_method' => 'PUT',
        'name' => 'Tech Supply Atualizada',
        'contact_name' => 'Marina Costa',
        'email' => 'contato@techsupply.test',
        'phone' => '(11) 98888-8888',
        'document' => '12.345.678/0001-99',
        'notes' => 'Fornecedor atualizado.',
        'is_active' => '0',
        'profile_photo' => fakePng('new-profile.png'),
    ], [
        'Accept' => 'application/json',
    ])
        ->assertOk()
        ->assertJsonPath('name', 'Tech Supply Atualizada')
        ->assertJsonPath('is_active', false);

    $supplier->refresh();

    Storage::disk('public')->assertMissing($oldPhotoPath);
    Storage::disk('public')->assertExists($supplier->profile_photo_path);
});

it('creates, updates and deletes products with image upload', function () {
    Storage::fake('public');

    $type = Type::create([
        'name' => 'Escritorio',
        'description' => 'Produtos de escritorio.',
    ]);

    $supplier = Supplier::create([
        'name' => 'Office Center',
        'contact_name' => 'Paulo Lima',
        'email' => 'office@test.local',
        'is_active' => true,
    ]);

    $createResponse = $this->post('/api/products', [
        'name' => 'Cadeira ergonomica',
        'description' => 'Produto para home office.',
        'quantity' => '12',
        'price' => '899.90',
        'type_id' => (string) $type->id,
        'supplier_id' => (string) $supplier->id,
        'photo' => fakePng('chair.png'),
    ], [
        'Accept' => 'application/json',
    ]);

    $createResponse
        ->assertCreated()
        ->assertJsonPath('type.name', 'Escritorio')
        ->assertJsonPath('supplier.name', 'Office Center');

    $product = Product::firstOrFail();
    $oldPhotoPath = $product->photo_path;

    Storage::disk('public')->assertExists($oldPhotoPath);

    $this->post("/api/products/{$product->id}", [
        '_method' => 'PUT',
        'name' => 'Cadeira ergonomica premium',
        'description' => 'Produto atualizado.',
        'quantity' => '8',
        'price' => '1099.90',
        'type_id' => (string) $type->id,
        'supplier_id' => (string) $supplier->id,
        'photo' => fakePng('chair-new.png'),
    ], [
        'Accept' => 'application/json',
    ])
        ->assertOk()
        ->assertJsonPath('name', 'Cadeira ergonomica premium');

    $product->refresh();

    Storage::disk('public')->assertMissing($oldPhotoPath);
    Storage::disk('public')->assertExists($product->photo_path);

    $newPhotoPath = $product->photo_path;

    $this->deleteJson("/api/products/{$product->id}")
        ->assertOk()
        ->assertJsonPath('message', 'Produto removido com sucesso.');

    Storage::disk('public')->assertMissing($newPhotoPath);
});
