<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('producten', function (Blueprint $table) {
            if (!Schema::hasColumn('producten', 'ean_code')) {
                $table->string('ean_code', 13)->nullable()->after('naam');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('producten', function (Blueprint $table) {
            if (Schema::hasColumn('producten', 'ean_code')) {
                $table->dropColumn('ean_code');
            }
        });
    }
};
