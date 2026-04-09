@extends('layouts.app')

@section('title', 'Editar Produto')

@section('content')
    <style>
        .form-container {
            max-width: 700px;
            margin: 0 auto;
            background: #2a2a2a;
            border: 1px solid #3a3a3a;
            border-radius: 8px;
            padding: 32px;
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.4);
        }
        .form-header {
            margin-bottom: 24px;
        }
        .form-header h1 {
            margin: 0;
            font-size: 1.8em;
        }
        .form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 16px;
            margin-bottom: 16px;
        }
        .form-group-full {
            grid-column: 1 / -1;
        }
        .form-group {
            margin-bottom: 0;
        }
        label {
            display: block;
            margin-bottom: 6px;
            color: #8b5cf6;
            font-weight: 600;
            font-size: 0.95em;
        }
        input, textarea {
            width: 100%;
            padding: 9px 12px;
            background: #1a1a1a;
            color: #e0e0e0;
            border: 1px solid #3a3a3a;
            border-radius: 4px;
            font-family: Arial, sans-serif;
            font-size: 0.95em;
            transition: all 0.2s;
        }
        input:focus, textarea:focus {
            outline: none;
            border-color: #8b5cf6;
            box-shadow: 0 0 8px rgba(139, 92, 246, 0.2);
            background: #252525;
        }
        textarea {
            resize: vertical;
            min-height: 80px;
        }
        .form-actions {
            display: flex;
            gap: 12px;
            margin-top: 28px;
            padding-top: 20px;
            border-top: 1px solid #3a3a3a;
        }
        .btn-primary {
            flex: 1;
            padding: 11px 16px;
            font-size: 0.98em;
        }
        .btn-secondary {
            padding: 11px 16px;
            background: #3a3a3a;
            color: #e0e0e0;
            border: none;
            border-radius: 4px;
            text-decoration: none;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.3s;
        }
        .btn-secondary:hover {
            background: #4a4a4a;
        }
        .error-message {
            color: #ff7675;
            font-size: 0.8em;
            margin-top: 4px;
            display: block;
        }
    </style>

<div class="form-container" style="background: #0f172a; border-color: #312e81;">
        <div class="form-header">
            <h1 style="color: #f8fafc;">Editar Produto</h1>
        </div>

        @if(session('error'))
            <div style="background: #3a1f1f; border-left: 4px solid #ff7675; color: #ff7675; padding: 12px 16px; margin-bottom: 20px; border-radius: 4px;">
                {{ session('error') }}
            </div>
        @endif

        <form action="{{ url('products/' . $product->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="form-grid">
                <div class="form-group">
                    <label for="name">Nome do Produto *</label>
                    <input id="name" name="name" type="text" value="{{ old('name', $product->name) }}" placeholder="Ex: Camiseta Premium" required />
                    @error('name')<span class="error-message">{{ $message }}</span>@enderror
                </div>

                <div class="form-group">
                    <label for="quantity">Quantidade *</label>
                    <input id="quantity" name="quantity" type="number" value="{{ old('quantity', $product->quantity) }}" placeholder="Ex: 10" min="0" required />
                    @error('quantity')<span class="error-message">{{ $message }}</span>@enderror
                </div>

                <div class="form-group">
                    <label for="price">Preço (R$) *</label>
                    <input id="price" name="price" type="number" value="{{ old('price', $product->price) }}" placeholder="Ex: 99.90" step="0.01" min="0" required />
                    @error('price')<span class="error-message">{{ $message }}</span>@enderror
                </div>

                <div class="form-group">
                    <label for="type_id">Tipo de Produto *</label>
                    <select id="type_id" name="type_id" required>
                        <option value="" disabled>Escolha um tipo</option>
                        @foreach($types as $type)
                            <option value="{{ $type->id }}" {{ old('type_id', $product->type_id) == $type->id ? 'selected' : '' }}>
                                {{ $type->name }} (ID: {{ $type->id }})
                            </option>
                        @endforeach
                    </select>
                    @error('type_id')<span class="error-message">{{ $message }}</span>@enderror
                </div>

                <div class="form-group form-group-full">
                    <label for="description">Descrição</label>
                    <textarea id="description" name="description" placeholder="Descrição do produto (opcional)">{{ old('description', $product->description) }}</textarea>
                    @error('description')<span class="error-message">{{ $message }}</span>@enderror
                </div>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn-primary">Atualizar Produto</button>
                <a href="{{ url('/products') }}" class="btn-secondary">Voltar</a>
            </div>
        </form>
    </div>
@endsection
