<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('products') || ! Schema::hasColumn('products', 'barcode')) {
            return;
        }

        DB::statement('ALTER TABLE products MODIFY barcode VARCHAR(255) NULL');

        DB::table('products')->where('barcode', '')->update(['barcode' => null]);
    }

    public function down(): void
    {
        if (! Schema::hasTable('products') || ! Schema::hasColumn('products', 'barcode')) {
            return;
        }

        DB::table('products')->whereNull('barcode')->update(['barcode' => '']);

        DB::statement('ALTER TABLE products MODIFY barcode VARCHAR(255) NOT NULL');
    }
};
