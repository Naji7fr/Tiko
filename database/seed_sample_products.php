<?php

require __DIR__ . '/../vendor/autoload.php';

$app = require __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

if (! Schema::hasColumn('producten', 'ean_code')) {
    DB::statement('ALTER TABLE producten ADD COLUMN ean_code VARCHAR(13) NOT NULL DEFAULT \'0000000000000\' AFTER naam');
    echo "Added ean_code column to producten.\n";
}

if (DB::table('producten')->count() > 0) {
    echo 'Products already exist: ' . DB::table('producten')->count() . PHP_EOL;
    exit(0);
}

$leverancierGegevensId = DB::table('leverancier_gegevens')->insertGetId([
    'bedrijfsnaam' => 'Barber Supply NL',
    'email' => 'info@barbersupply.nl',
    'telefoon' => '0201234567',
    'created_at' => now(),
    'updated_at' => now(),
]);

$leverancierId = DB::table('leveranciers')->insertGetId([
    'leverancier_gegevens_id' => $leverancierGegevensId,
    'created_at' => now(),
    'updated_at' => now(),
]);

$categorieen = DB::table('categorieen')->pluck('id', 'naam');

$producten = [
    ['cat' => 'Haarproducten', 'naam' => 'Pomade Classic', 'ean' => '8712345678901', 'prijs' => 14.95, 'voorraad' => 25],
    ['cat' => 'Baardverzorging', 'naam' => 'Baardolie Premium', 'ean' => '8712345678902', 'prijs' => 19.95, 'voorraad' => 18],
    ['cat' => 'Verzorging', 'naam' => 'Aftershave Balm', 'ean' => '8712345678903', 'prijs' => 12.50, 'voorraad' => 30],
];

foreach ($producten as $product) {
    $categorieId = $categorieen[$product['cat']] ?? DB::table('categorieen')->value('id');

    $productId = DB::table('producten')->insertGetId([
        'categorie_id' => $categorieId,
        'leverancier_id' => $leverancierId,
        'naam' => $product['naam'],
        'ean_code' => $product['ean'],
        'beschrijving' => null,
        'prijs' => $product['prijs'],
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    DB::table('voorraad')->insert([
        'product_id' => $productId,
        'categorie_id' => $categorieId,
        'aantal' => $product['voorraad'],
        'minimum' => 5,
        'created_at' => now(),
        'updated_at' => now(),
    ]);
}

echo 'Seeded 3 sample products.' . PHP_EOL;
