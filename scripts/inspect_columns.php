<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
// Bootstrap kernel for DB
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();
$rows = DB::select('SHOW COLUMNS FROM producten');
foreach ($rows as $r) {
    echo $r->Field . PHP_EOL;
}
