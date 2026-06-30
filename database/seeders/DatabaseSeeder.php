<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Product;
use App\Models\Supplier;
use App\Models\Type;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::firstOrCreate(
            ['email' => 'test@example.com'],
            [
                'name' => 'Test User',
                'password' => 'password',
            ]
        );

        $electronics = Type::firstOrCreate(
            ['name' => 'Eletronicos'],
            ['description' => 'Produtos de tecnologia e equipamentos digitais.']
        );

        $office = Type::firstOrCreate(
            ['name' => 'Escritorio'],
            ['description' => 'Itens para rotina administrativa e home office.']
        );

        $furniture = Type::firstOrCreate(
            ['name' => 'Mobiliario'],
            ['description' => 'Moveis e itens de infraestrutura para ambientes de trabalho.']
        );

        $peripherals = Type::firstOrCreate(
            ['name' => 'Perifericos'],
            ['description' => 'Acessorios e dispositivos de entrada e saida.']
        );

        $techSupply = Supplier::firstOrCreate(
            ['email' => 'contato@techsupply.test'],
            [
                'name' => 'Tech Supply',
                'contact_name' => 'Marina Costa',
                'phone' => '(11) 99999-9999',
                'document' => '12.345.678/0001-99',
                'is_active' => true,
                'notes' => 'Fornecedor principal para tecnologia.',
            ]
        );

        $officeCenter = Supplier::firstOrCreate(
            ['email' => 'comercial@officecenter.test'],
            [
                'name' => 'Office Center',
                'contact_name' => 'Paulo Lima',
                'phone' => '(11) 98888-8888',
                'document' => '98.765.432/0001-10',
                'is_active' => true,
                'notes' => 'Fornecedor de itens administrativos.',
            ]
        );

        $megaGlobal = Supplier::firstOrCreate(
            ['email' => 'vendas@megaglobal.test'],
            [
                'name' => 'Mega Global Atacado',
                'contact_name' => 'Ricardo Souza',
                'phone' => '(11) 97777-7777',
                'document' => '55.444.333/0001-22',
                'is_active' => true,
                'notes' => 'Importadora de perifericos e acessorios.',
            ]
        );

        $nexusMovel = Supplier::firstOrCreate(
            ['email' => 'contato@nexusmovel.test'],
            [
                'name' => 'Nexus Moveis Corp',
                'contact_name' => 'Beatriz Rocha',
                'phone' => '(11) 96666-6666',
                'document' => '11.222.333/0001-44',
                'is_active' => false,
                'notes' => 'Fornecedor de mobiliario (atualmente em renegociacao).',
            ]
        );

        Product::firstOrCreate(
            ['name' => 'Notebook corporativo'],
            [
                'description' => 'Equipamento para equipe administrativa.',
                'quantity' => 7,
                'price' => 3899.90,
                'type_id' => $electronics->id,
                'supplier_id' => $techSupply->id,
            ]
        );

        Product::firstOrCreate(
            ['name' => 'Monitor UltraWide'],
            [
                'description' => 'Monitor para produtividade e atendimento.',
                'quantity' => 4,
                'price' => 1499.90,
                'type_id' => $electronics->id,
                'supplier_id' => $techSupply->id,
            ]
        );

        Product::firstOrCreate(
            ['name' => 'Cadeira ergonomica'],
            [
                'description' => 'Cadeira para operacao de escritorio.',
                'quantity' => 12,
                'price' => 899.90,
                'type_id' => $office->id,
                'supplier_id' => $officeCenter->id,
            ]
        );

        Product::firstOrCreate(
            ['name' => 'Kit papelaria'],
            [
                'description' => 'Materiais de apoio para expediente.',
                'quantity' => 3,
                'price' => 129.90,
                'type_id' => $office->id,
                'supplier_id' => $officeCenter->id,
            ]
        );

        Product::firstOrCreate(
            ['name' => 'Teclado Mecanico RGB'],
            [
                'description' => 'Teclado mecanico switch blue para desenvolvedores.',
                'quantity' => 15,
                'price' => 349.90,
                'type_id' => $peripherals->id,
                'supplier_id' => $megaGlobal->id,
            ]
        );

        Product::firstOrCreate(
            ['name' => 'Mouse Gamer Wireless'],
            [
                'description' => 'Mouse com sensor de alta precisao.',
                'quantity' => 20,
                'price' => 199.90,
                'type_id' => $peripherals->id,
                'supplier_id' => $megaGlobal->id,
            ]
        );

        Product::firstOrCreate(
            ['name' => 'Mesa de Escritorio em L'],
            [
                'description' => 'Mesa ampla com acabamento em madeira.',
                'quantity' => 2,
                'price' => 1250.00,
                'type_id' => $furniture->id,
                'supplier_id' => $nexusMovel->id,
            ]
        );

        Product::firstOrCreate(
            ['name' => 'Switch Gigabit 24 Portas'],
            [
                'description' => 'Equipamento de rede para infraestrutura.',
                'quantity' => 1,
                'price' => 850.00,
                'type_id' => $electronics->id,
                'supplier_id' => $techSupply->id,
            ]
        );
    }
}
