<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Type;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class TypeController extends Controller
{
    public function index(): JsonResponse
    {
        $types = Type::withCount('products')
            ->orderBy('name')
            ->get();

        return response()->json($types);
    }

    public function show(Type $type): JsonResponse
    {
        return response()->json($type->loadCount('products'));
    }

    public function store(Request $request): JsonResponse
    {
        $type = Type::create($this->validatedData($request));

        return response()->json($type->loadCount('products'), 201);
    }

    public function update(Request $request, Type $type): JsonResponse
    {
        $type->update($this->validatedData($request, $type));

        return response()->json($type->fresh()->loadCount('products'));
    }

    public function destroy(Type $type): JsonResponse
    {
        if ($type->products()->exists()) {
            return response()->json([
                'message' => 'Nao e possivel remover um tipo que possui produtos vinculados.',
            ], 422);
        }

        $type->delete();

        return response()->json([
            'message' => 'Tipo removido com sucesso.',
        ]);
    }

    protected function validatedData(Request $request, ?Type $type = null): array
    {
        return $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('types', 'name')->ignore($type?->id),
            ],
            'description' => ['nullable', 'string', 'max:1000'],
        ]);
    }
}
