<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DemoProductSeeder extends Seeder
{
    public function run()
    {
        if (DB::table('producten')->count() === 0) {
            $categorie = DB::table('categorieen')->first();
            $leverancier = DB::table('leveranciers')->first();

            if ($categorie && $leverancier) {
                $pid = DB::table('producten')->insertGetId([
                    'categorie_id' => $categorie->id,
                    'leverancier_id' => $leverancier->id,
                    'naam' => 'Demo Shampoo',
                    'beschrijving' => 'Voorbeeldproduct',
                    'prijs' => 9.99,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                DB::table('voorraad')->insert([
                    'product_id' => $pid,
                    'categorie_id' => $categorie->id,
                    'aantal' => 10,
                    'minimum' => 5,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }
}
