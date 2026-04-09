<?php

namespace App\Http\Controllers\Api;

use App\Models\Type;
use Illuminate\Http\Request;

class TypeController
{
    public function index()
    {
        return Type::all();
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:types,name'
        ]);

        $type = Type::create($validated);

        return response()->json($type, 201);
    }
}
