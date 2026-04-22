<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Supplier;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class SupplierController extends Controller
{
    public function index(): JsonResponse
    {
        $suppliers = Supplier::withCount('products')
            ->latest()
            ->get();

        return response()->json($suppliers);
    }

    public function show(Supplier $supplier): JsonResponse
    {
        return response()->json($supplier->loadCount('products'));
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $this->validatedData($request);

        if ($request->hasFile('profile_photo')) {
            $validated['profile_photo_path'] = $request->file('profile_photo')->store('suppliers', 'public');
        }

        $supplier = Supplier::create($validated);

        return response()->json($supplier->fresh()->loadCount('products'), 201);
    }

    public function update(Request $request, Supplier $supplier): JsonResponse
    {
        $validated = $this->validatedData($request, $supplier);

        if ($request->boolean('remove_photo') && $supplier->profile_photo_path) {
            Storage::disk('public')->delete($supplier->profile_photo_path);
            $validated['profile_photo_path'] = null;
        }

        if ($request->hasFile('profile_photo')) {
            if ($supplier->profile_photo_path) {
                Storage::disk('public')->delete($supplier->profile_photo_path);
            }

            $validated['profile_photo_path'] = $request->file('profile_photo')->store('suppliers', 'public');
        }

        $supplier->update($validated);

        return response()->json($supplier->fresh()->loadCount('products'));
    }

    public function destroy(Supplier $supplier): JsonResponse
    {
        if ($supplier->profile_photo_path) {
            Storage::disk('public')->delete($supplier->profile_photo_path);
        }

        $supplier->delete();

        return response()->json([
            'message' => 'Fornecedor removido com sucesso.',
        ]);
    }

    protected function validatedData(Request $request, ?Supplier $supplier = null): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'contact_name' => ['nullable', 'string', 'max:255'],
            'email' => [
                'nullable',
                'email',
                'max:255',
                Rule::unique('suppliers', 'email')->ignore($supplier?->id),
            ],
            'phone' => ['nullable', 'string', 'max:30'],
            'document' => ['nullable', 'string', 'max:40'],
            'notes' => ['nullable', 'string', 'max:4000'],
            'is_active' => ['nullable', 'boolean'],
            'profile_photo' => ['nullable', 'image', 'max:3072'],
            'remove_photo' => ['nullable', 'boolean'],
        ]);
    }
}
