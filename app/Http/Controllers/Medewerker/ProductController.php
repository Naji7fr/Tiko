<?php

namespace App\Http\Controllers\Medewerker;

use App\Http\Controllers\Controller;
use App\Models\Medewerker\TechnischeLogModel;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ProductController extends Controller
{
    /**
     * GET /product-overzicht — Productenlijst voor medewerkers.
     */
    public function index(): View|RedirectResponse
    {
        try {
            $producten = DB::table('producten as p')
                ->leftJoin('categorieen as c', 'p.categorie_id', '=', 'c.id')
                ->leftJoin('leveranciers as l', 'p.leverancier_id', '=', 'l.id')
                ->leftJoin('leverancier_gegevens as vg', 'l.leverancier_gegevens_id', '=', 'vg.id')
                ->leftJoin('voorraad as v', 'p.id', '=', 'v.product_id')
                ->select(
                    'p.id',
                    'p.naam as product_naam',
                    'c.naam as categorie_naam',
                    'vg.bedrijfsnaam as leverancier_naam',
                    'v.aantal as voorraad'
                )
                ->orderBy('p.naam')
                ->get()
                ->map(function ($product) {
                    $product->voorraad = $product->voorraad ?? 0;
                    $product->ean_code = str_pad((string) ($product->id ?? 0), 13, '0', STR_PAD_LEFT);

                    return $product;
                });

            return view('medewerker.producten.index', compact('producten'));
        } catch (\Throwable $exception) {
            TechnischeLogModel::registreer('error', 'producten', 'index', $exception->getMessage());

            return redirect()->route('medewerkers.index')
                ->with('error', 'Productoverzicht kon niet worden geladen.');
        }
    }
}
