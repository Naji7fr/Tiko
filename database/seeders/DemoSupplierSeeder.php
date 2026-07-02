<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DemoSupplierSeeder extends Seeder
{
    public function run()
    {
        if (DB::table('leveranciers')->count() === 0) {
            $lid = DB::table('leverancier_gegevens')->insertGetId([
                'bedrijfsnaam' => 'Demo Leverancier',
                'email' => 'demo@leverancier.nl',
                'telefoon' => '0000-000000',
                'kvk_nummer' => '12345678',
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            DB::table('leveranciers')->insert([
                'leverancier_gegevens_id' => $lid,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
