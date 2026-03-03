<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reizen', function (Blueprint $table) {
            $table->id();
            $table->string('titel');
            $table->string('bestemming');
            $table->date('start_datum');
            $table->date('eind_datum');
            $table->string('status')->default('beschikbaar'); // beschikbaar, voltooid, geannuleerd
            $table->text('beschrijving')->nullable();
            $table->decimal('prijs', 10, 2)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reizen');
    }
};
