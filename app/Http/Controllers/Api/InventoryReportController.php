<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Supplier;
use App\Models\Type;
use Illuminate\Http\JsonResponse;

class InventoryReportController extends Controller
{
    public function __invoke(): JsonResponse
    {
        $products = Product::with(['type', 'supplier'])->latest()->get();
        $types = Type::withCount('products')->orderBy('name')->get();
        $suppliers = Supplier::withCount('products')->latest()->get();

        $inventoryValue = $products->sum(
            fn (Product $product): float => (float) $product->price * (int) $product->quantity
        );

        $productsByType = $types->map(fn (Type $type): array => [
            'id' => $type->id,
            'name' => $type->name,
            'products_count' => $type->products_count,
            'stock_quantity' => $products
                ->where('type_id', $type->id)
                ->sum(fn (Product $product): int => (int) $product->quantity),
            'stock_value' => $products
                ->where('type_id', $type->id)
                ->sum(fn (Product $product): float => (float) $product->price * (int) $product->quantity),
        ])->values();

        $productsBySupplier = $suppliers->map(fn (Supplier $supplier): array => [
            'id' => $supplier->id,
            'name' => $supplier->name,
            'is_active' => $supplier->is_active,
            'products_count' => $supplier->products_count,
            'stock_quantity' => $products
                ->where('supplier_id', $supplier->id)
                ->sum(fn (Product $product): int => (int) $product->quantity),
            'stock_value' => $products
                ->where('supplier_id', $supplier->id)
                ->sum(fn (Product $product): float => (float) $product->price * (int) $product->quantity),
        ])->values();

        return response()->json([
            'summary' => [
                'products_count' => $products->count(),
                'types_count' => $types->count(),
                'suppliers_count' => $suppliers->count(),
                'active_suppliers_count' => $suppliers->where('is_active', true)->count(),
                'low_stock_count' => $products->where('quantity', '<=', 5)->count(),
                'inventory_value' => $inventoryValue,
            ],
            'products_by_type' => $productsByType,
            'products_by_supplier' => $productsBySupplier,
            'low_stock_products' => $products
                ->where('quantity', '<=', 5)
                ->sortBy('quantity')
                ->values()
                ->take(10),
            'recent_products' => $products->take(10)->values(),
        ]);
    }
}
