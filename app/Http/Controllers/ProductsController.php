<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Type;
use Illuminate\Http\Request;

class ProductsController extends Controller
{
    //
    public function create()
    {
        $types = Type::all();

        return view('products.create', compact('types'));
    }

    // função chamada no submit do form..
    // será um POST com os dados
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'quantity' => 'required|integer|min:0',
            'price' => 'required|numeric|min:0',
            'type_id' => 'required|integer|exists:types,id',
        ]);

        Product::create($data);

        return redirect()->back()->with('success', 'Produto salvo com sucesso!');
    }

    // função que irá mostrar a view de listagem
    // passando como parâmetro a consulta no banco com ::all()
    public function index()
    {
        return view('products.index', [
            'products' => Product::with('type')->get(),
        ]);
    }

    public function edit($id)
    {
        $product = Product::find($id);

        if (!$product) {
            return redirect('/products')->with('error', 'Produto não encontrado');
        }

        $types = Type::all();
        return view('products.edit', compact('product', 'types'));
    }

    public function update(Request $request, $id)
    {
        $product = Product::find($id);

        if (!$product) {
            return redirect('/products')->with('error', 'Produto não encontrado');
        }

        $data = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'quantity' => 'required|integer|min:0',
            'price' => 'required|numeric|min:0',
            'type_id' => 'required|integer|min:1',
        ]);

        try {
            // Garante que o type_id selecionado existe
            $request->validate([
                'type_id' => 'required|integer|exists:types,id',
            ]);

            // Atualiza o produto
            $product->fill($data);
            $product->save();

            return redirect('/products')->with('success', 'Produto atualizado com sucesso!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Erro ao atualizar: ' . $e->getMessage());
        }
    }
    public function destroy($id)
    {
        $product = Product::find($id);

        if (!$product) {
            return redirect('/products')->with('error', 'Produto não encontrado');
        }

        try {
            $product->delete();
            return redirect('/products')->with('success', 'Produto excluído com sucesso!');
        } catch (\Exception $e) {
            return redirect('/products')->with('error', 'Erro ao excluir produto: ' . $e->getMessage());
        }
    }

}
