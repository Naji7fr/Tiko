<?php

namespace App\Http\Controllers\Medewerker;

use App\Http\Controllers\Controller;
use App\Http\Requests\Medewerker\StoreProductRequest;
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

    /**
     * GET /product-toevoegen — Formulier voor nieuw product.
     */
    public function create(): View
    {
        $categorieen = DB::table('categorieen')->orderBy('naam')->get();
        $leveranciers = DB::table('leveranciers as l')
            ->join('leverancier_gegevens as lg', 'l.leverancier_gegevens_id', '=', 'lg.id')
            ->select('l.id', 'lg.bedrijfsnaam')
            ->orderBy('lg.bedrijfsnaam')
            ->get();

        return view('medewerker.producten.create', compact('categorieen', 'leveranciers'));
    }

    /**
     * POST /product-toevoegen — Sla nieuw product op.
     */
    public function store(StoreProductRequest $request): RedirectResponse
    {
        try {
            DB::transaction(function () use ($request) {
                $productId = DB::table('producten')->insertGetId([
                    'categorie_id' => $request->input('categorie_id'),
                    'leverancier_id' => $request->input('leverancier_id'),
                    'naam' => $request->input('naam'),
                    'beschrijving' => $request->input('beschrijving'),
                    'prijs' => $request->input('prijs') ?? 0,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                DB::table('voorraad')->insert([
                    'product_id' => $productId,
                    'categorie_id' => $request->input('categorie_id'),
                    'aantal' => $request->input('voorraad'),
                    'minimum' => 5,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            });

            return redirect()
                ->route('producten.index')
                ->with('success', 'Product succesvol toegevoegd!');
        } catch (\Throwable $exception) {
            TechnischeLogModel::registreer('error', 'producten', 'store', $exception->getMessage());

            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Product kon niet worden toegevoegd. Controleer de gegevens.');
        }
    }
}
