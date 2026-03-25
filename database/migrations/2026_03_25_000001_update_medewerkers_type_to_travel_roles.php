<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // The original create migration now uses the new type values.
        // This migration only runs on databases that still had old data before migrate:fresh.
    }

    public function down(): void
    {
        // Nothing to reverse.
    }
};
