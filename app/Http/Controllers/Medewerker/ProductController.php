<?php

namespace App\Http\Controllers\Medewerker;

use App\Http\Controllers\Controller;
use App\Models\Medewerker\TechnischeLogModel;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;

class ProductController extends Controller
{
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
                    'p.ean_code',
                    'c.naam as categorie_naam',
                    'vg.bedrijfsnaam as leverancier_naam',
                    'v.aantal as voorraad'
                )
                ->orderBy('p.naam')
                ->get()
                ->map(function ($product) {
                    $product->voorraad = $product->voorraad ?? 0;
                    $product->ean_code = $product->ean_code ?? str_pad((string) ($product->id ?? 0), 13, '0', STR_PAD_LEFT);

                    return $product;
                });

            return view('medewerker.producten.index', compact('producten'));
        } catch (\Throwable $exception) {
            TechnischeLogModel::registreer('error', 'producten', 'index', $exception->getMessage());

            return redirect()->route('medewerkers.index')
                ->with('error', 'Productoverzicht kon niet worden geladen.');
        }
    }

    public function destroy(int $id): RedirectResponse
    {
        try {
            $inUse = DB::table('behandeling_producten')->where('product_id', $id)->exists()
                || DB::table('bestelling_producten')->where('product_id', $id)->exists();

            if ($inUse) {
                return redirect()->route('producten.index')
                    ->with('error', 'Dit product kan niet worden verwijderd omdat het nog in gebruik is');
            }

            DB::transaction(function () use ($id) {
                DB::table('producten')->where('id', $id)->delete();
            });

            return redirect()->route('producten.index')
                ->with('success', 'Product succesvol verwijderd!');
        } catch (\Throwable $exception) {
            TechnischeLogModel::registreer('error', 'producten', 'destroy', $exception->getMessage());

            return redirect()->route('producten.index')
                ->with('error', 'Product kon niet worden verwijderd.');
        }
    }

    public function create(): View|RedirectResponse
    {
        try {
            $categorieen = DB::table('categorieen')->orderBy('naam')->get();
            $leveranciers = DB::table('leveranciers as l')
                ->leftJoin('leverancier_gegevens as vg', 'l.leverancier_gegevens_id', '=', 'vg.id')
                ->select('l.id', 'vg.bedrijfsnaam')
                ->orderBy('vg.bedrijfsnaam')
                ->get();

            return view('medewerker.producten.create', compact('categorieen', 'leveranciers'));
        } catch (\Throwable $exception) {
            TechnischeLogModel::registreer('error', 'producten', 'create', $exception->getMessage());

            return redirect()->route('producten.index')
                ->with('error', 'Productformulier kon niet geladen worden.');
        }
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'naam' => 'required|string|max:100',
            'categorie_id' => 'required|exists:categorieen,id',
            'leverancier_id' => 'required|exists:leveranciers,id',
            'prijs' => 'required|numeric',
            'beschrijving' => 'nullable|string|max:225'
        ]);

        try {
            DB::transaction(function () use ($data) {
                $id = DB::table('producten')->insertGetId([
                    'categorie_id' => $data['categorie_id'],
                    'leverancier_id' => $data['leverancier_id'],
                    'naam' => $data['naam'],
                    'beschrijving' => $data['beschrijving'] ?? null,
                    'prijs' => $data['prijs'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                DB::table('voorraad')->insert([
                    'product_id' => $id,
                    'categorie_id' => $data['categorie_id'],
                    'aantal' => 0,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            });

            return redirect()->route('producten.index')->with('success', 'Product succesvol toegevoegd.');
        } catch (\Throwable $exception) {
            TechnischeLogModel::registreer('error', 'producten', 'store', $exception->getMessage());

            return redirect()->route('producten.index')->with('error', 'Product kon niet worden toegevoegd.');
        }
    }

    public function edit(int $id): View|RedirectResponse
    {
        try {
            $product = DB::table('producten')->where('id', $id)->first();

            if (!$product) {
                return redirect()->route('producten.index')->with('error', 'Product niet gevonden.');
            }

            $categorieen = DB::table('categorieen')->orderBy('naam')->get();
            $leveranciers = DB::table('leveranciers as l')
                ->leftJoin('leverancier_gegevens as vg', 'l.leverancier_gegevens_id', '=', 'vg.id')
                ->select('l.id', 'vg.bedrijfsnaam')
                ->orderBy('vg.bedrijfsnaam')
                ->get();

            return view('medewerker.producten.edit', compact('product', 'categorieen', 'leveranciers'));
        } catch (\Throwable $exception) {
            TechnischeLogModel::registreer('error', 'producten', 'edit', $exception->getMessage());

            return redirect()->route('producten.index')->with('error', 'Product kon niet worden geladen.');
        }
    }

    public function update(Request $request, int $id): RedirectResponse
    {
        $data = $request->validate([
            'naam' => 'required|string|max:100',
            'categorie_id' => 'required|exists:categorieen,id',
            'leverancier_id' => 'required|exists:leveranciers,id',
            'prijs' => 'required|numeric',
            'beschrijving' => 'nullable|string|max:225'
        ]);

        try {
            DB::transaction(function () use ($id, $data) {
                DB::table('producten')->where('id', $id)->update([
                    'categorie_id' => $data['categorie_id'],
                    'leverancier_id' => $data['leverancier_id'],
                    'naam' => $data['naam'],
                    'beschrijving' => $data['beschrijving'] ?? null,
                    'prijs' => $data['prijs'],
                    'updated_at' => now(),
                ]);

                DB::table('voorraad')->where('product_id', $id)->update(['categorie_id' => $data['categorie_id'], 'updated_at' => now()]);
            });

            return redirect()->route('producten.index')->with('success', 'Product succesvol bijgewerkt.');
        } catch (\Throwable $exception) {
            TechnischeLogModel::registreer('error', 'producten', 'update', $exception->getMessage());

            return redirect()->route('producten.index')->with('error', 'Product kon niet worden bijgewerkt.');
        }
    }
}
