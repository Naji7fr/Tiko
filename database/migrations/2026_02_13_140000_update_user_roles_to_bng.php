<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Map old roles to new BNG roles: Klant, Reisadviseur, Financieel medewerker, Manager operations.
     */
    public function up(): void
    {
        DB::table('users')->where('role', 'patient')->update(['role' => 'klant']);
        DB::table('users')->where('role', 'tandarts')->update(['role' => 'reisadviseur']);
        DB::table('users')->whereIn('role', ['admin', 'praktijkmanager'])->update(['role' => 'manager_operations']);
        DB::table('users')->where('role', 'assistent')->update(['role' => 'reisadviseur']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('users')->where('role', 'klant')->update(['role' => 'patient']);
        DB::table('users')->where('role', 'reisadviseur')->update(['role' => 'tandarts']);
        DB::table('users')->where('role', 'manager_operations')->update(['role' => 'admin']);
        // Note: we cannot perfectly restore assistent/praktijkmanager
    }
};
