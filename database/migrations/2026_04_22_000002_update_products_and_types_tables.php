<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('types', function (Blueprint $table) {
            if (! Schema::hasColumn('types', 'description')) {
                $table->text('description')->nullable()->after('name');
            }
        });

        Schema::table('products', function (Blueprint $table) {
            if (! Schema::hasColumn('products', 'supplier_id')) {
                $table->foreignId('supplier_id')->nullable()->after('type_id')->constrained()->nullOnDelete();
            }

            if (! Schema::hasColumn('products', 'photo_path')) {
                $table->string('photo_path')->nullable()->after('supplier_id');
            }
        });

        Schema::table('products', function (Blueprint $table) {
            $table->text('description')->nullable()->change();
            $table->decimal('price', 10, 2)->change();
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            if (Schema::hasColumn('products', 'photo_path')) {
                $table->dropColumn('photo_path');
            }

            if (Schema::hasColumn('products', 'supplier_id')) {
                $table->dropConstrainedForeignId('supplier_id');
            }

            $table->string('description')->nullable()->change();
            $table->decimal('price', 8, 2)->change();
        });

        Schema::table('types', function (Blueprint $table) {
            if (Schema::hasColumn('types', 'description')) {
                $table->dropColumn('description');
            }
        });
    }
};
