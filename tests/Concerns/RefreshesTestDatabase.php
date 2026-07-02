<?php

namespace Tests\Concerns;

use Illuminate\Support\Facades\DB;

/**
 * Laadt het SQLite-testschema vóór elke test (geen Laravel migrations).
 */
trait RefreshesTestDatabase
{
    protected function setUpRefreshesTestDatabase(): void
    {
        DB::unprepared((string) file_get_contents(database_path('schema/testing.sql')));
    }
}
