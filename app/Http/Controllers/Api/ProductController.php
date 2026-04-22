<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    public function index(): JsonResponse
    {
        $products = Product::with(['type', 'supplier'])
            ->latest()
            ->get();

        return response()->json($products);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $this->validatedData($request);

        if ($request->hasFile('photo')) {
            $validated['photo_path'] = $request->file('photo')->store('products', 'public');
        }

        $product = Product::create($validated);

        return response()->json($product->fresh()->load(['type', 'supplier']), 201);
    }

    public function show(Product $product): JsonResponse
    {
        return response()->json($product->load(['type', 'supplier']));
    }

    public function update(Request $request, Product $product): JsonResponse
    {
        $validated = $this->validatedData($request);

        if ($request->boolean('remove_photo') && $product->photo_path) {
            Storage::disk('public')->delete($product->photo_path);
            $validated['photo_path'] = null;
        }

        if ($request->hasFile('photo')) {
            if ($product->photo_path) {
                Storage::disk('public')->delete($product->photo_path);
            }

            $validated['photo_path'] = $request->file('photo')->store('products', 'public');
        }

        $product->update($validated);

        return response()->json($product->fresh()->load(['type', 'supplier']));
    }

    public function destroy(Product $product): JsonResponse
    {
        if ($product->photo_path) {
            Storage::disk('public')->delete($product->photo_path);
        }

        $product->delete();

        return response()->json([
            'message' => 'Produto removido com sucesso.',
        ]);
    }

    protected function validatedData(Request $request): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:4000'],
            'quantity' => ['required', 'integer', 'min:0'],
            'price' => ['required', 'numeric', 'min:0'],
            'type_id' => ['required', 'exists:types,id'],
            'supplier_id' => ['nullable', 'exists:suppliers,id'],
            'photo' => ['nullable', 'image', 'max:4096'],
            'remove_photo' => ['nullable', 'boolean'],
        ]);
    }
}
