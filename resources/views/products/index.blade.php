@extends('layouts.app')

@section('title', 'Produtos')

@section('content')
    <div class="header-actions">
        <h1>Produtos Cadastrados</h1>
        <a href="{{ url('/products/new') }}" class="btn-primary">+ Novo Produto</a>
    </div>

    @if(session('error'))
        <div style="background: #3a1f1f; border-left: 4px solid #ff7675; color: #ff7675; padding: 12px 16px; margin-bottom: 20px; border-radius: 4px;">
            {{ session('error') }}
        </div>
    @endif

    @if($products->isEmpty())
        <div class="empty-state" style="background: #0f172a; border: 1px solid #312e81; color: #e2e8f0; padding: 24px; border-radius: 12px;">
            <p class="text-slate-100">Nenhum produto cadastrado ainda.</p>
            <a href="{{ url('/products/new') }}" class="btn-primary">Criar Primeiro Produto</a>
        </div>
    @else
        <table style="width: 100%; border-collapse: collapse; background: #020617; border: 1px solid #312e81; border-radius: 12px; overflow: hidden;">
            <thead style="background: #111827; color: #e5e7eb;">
                <tr>
                    <th>ID</th>
                    <th>Nome</th>
                    <th>Descrição</th>
                    <th>Quantidade</th>
                    <th>Preço</th>
                    <th>Tipo</th>
                    <th>Criado em</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody style="color: #e5e7eb;">

                @foreach($products as $product)
                    <tr>
                        <td>{{ $product->id }}</td>
                        <td><strong>{{ $product->name }}</strong></td>
                        <td>{{ $product->description ?? '-' }}</td>
                        <td>{{ $product->quantity }}</td>
                        <td>R$ {{ number_format($product->price, 2, ',', '.') }}</td>
                        <td>{{ $product->type->name ?? ('Tipo ' . $product->type_id) }}</td>
                        <td>{{ $product->created_at->format('d/m/Y H:i') }}</td>
                        <td>
                            <a href="{{ url('products/' . $product->id . '/edit') }}" style="color: #8b5cf6; text-decoration: none; font-weight: 600; margin-right: 10px;">Editar</a>
                            <form action="{{ url('products/' . $product->id) }}" method="POST" style="display: inline;" onsubmit="return confirm('Tem certeza que deseja excluir este produto?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" style="background: none; border: none; color: #ff7675; text-decoration: none; font-weight: 600; cursor: pointer; padding: 0;">Excluir</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
@endsection