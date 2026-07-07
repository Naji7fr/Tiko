<?php

namespace App\Http\Controllers\Eigenaar;

use App\Http\Controllers\Controller;
use App\Models\Klant;
use App\Models\Klant\AdresModel;
use App\Models\Medewerker\ContactGegevensModel;
use App\Models\Medewerker\GebruikerModel;
use App\Models\Medewerker\TechnischeLogModel;
use App\Models\User;
use App\Services\Eigenaar\KlantenService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

/**
 * klanten.controller — MVC Controller voor klantenbeheer (eigenaar).
 *
 * Architectuur:
 *   View  ← Controller ← Service ← Model / Stored Procedures
 *
 * Overzicht + wijzigen/verwijderen van klantgegevens voor eigenaar.
 * Security: auth + role:admin middleware.
 */
class KlantenController extends Controller
{
    public function __construct(
        private readonly KlantenService $klantenService
    ) {}

    /**
     * GET /eigenaar/klanten — Overzicht klantgegevens via sp_klant_overzicht of JOINs.
     */
    public function index(): View|RedirectResponse
    {
        try {
            $klanten = $this->klantenService->haalKlantenOp();
            $zoekterm = trim((string) request('zoek', ''));
            $totaalKlanten = $klanten->count();

            if ($zoekterm !== '') {
                $klanten = $klanten
                    ->filter(static fn (object $klant): bool => stripos((string) ($klant->volledig_naam ?? ''), $zoekterm) !== false)
                    ->values();
            }

            return view('eigenaar.klant.index.view', compact('klanten', 'zoekterm', 'totaalKlanten'));
        } catch (\Throwable $exception) {
            TechnischeLogModel::registreer('error', 'klanten', 'index', $exception->getMessage());

            return redirect()->route('eigenaar.dashboard')
                ->with('error', 'Klantenoverzicht kon niet worden geladen.');
        }
    }

    /**
     * GET /eigenaar/klanten/toevoegen — Formulier om een klantrecord toe te voegen.
     */
    public function create(): View
    {
        return view('eigenaar.klant.create.view', [
            'form' => [
                'voornaam' => old('voornaam'),
                'achternaam' => old('achternaam'),
                'email' => old('email'),
                'telefoon' => old('telefoon'),
                'straat' => old('straat'),
                'huisnummer' => old('huisnummer'),
                'postcode' => old('postcode'),
                'plaats' => old('plaats'),
                'land' => old('land', 'Nederland'),
            ],
        ]);
    }

    /**
     * POST /eigenaar/klanten — Maak een klantrecord aan zonder loginaccount.
     */
    public function store(Request $request): RedirectResponse
    {
        try {
            $validated = $request->validate([
                'voornaam' => ['required', 'string', 'max:50', 'regex:/^[\pL\s\-\'\.]+$/u'],
                'achternaam' => ['required', 'string', 'max:50', 'regex:/^[\pL\s\-\'\.]+$/u'],
                'email' => ['required', 'email', 'max:254', 'unique:contact_gegevens,email', 'unique:users,email'],
                'telefoon' => ['nullable', 'string', 'max:25', 'regex:/^(?:\+31|0031|0)[1-9][0-9]{8}$/'],
                'straat' => ['nullable', 'string', 'max:100', 'required_with:huisnummer,postcode,plaats'],
                'huisnummer' => ['nullable', 'string', 'max:10', 'required_with:straat,postcode,plaats'],
                'postcode' => ['nullable', 'string', 'max:10', 'required_with:straat,huisnummer,plaats', 'regex:/^[1-9][0-9]{3}\s?[A-Za-z]{2}$/'],
                'plaats' => ['nullable', 'string', 'max:50', 'required_with:straat,huisnummer,postcode'],
                'land' => ['nullable', 'string', 'max:50'],
            ]);

            DB::transaction(function () use ($validated): void {
                $adresId = null;

                $heeftAdresData = collect([
                    $validated['straat'] ?? null,
                    $validated['huisnummer'] ?? null,
                    $validated['postcode'] ?? null,
                    $validated['plaats'] ?? null,
                ])->filter(static fn ($waarde) => filled($waarde))->isNotEmpty();

                if ($heeftAdresData) {
                    $adres = AdresModel::query()->create([
                        'straat' => $validated['straat'],
                        'huisnummer' => $validated['huisnummer'],
                        'postcode' => $validated['postcode'],
                        'plaats' => $validated['plaats'],
                        'land' => $validated['land'] ?: 'Nederland',
                    ]);

                    $adresId = $adres->id;
                }

                $contact = ContactGegevensModel::query()->create([
                    'email' => $validated['email'],
                    'telefoon' => $validated['telefoon'] ?: null,
                ]);

                $gebruiker = GebruikerModel::query()->create([
                    'contact_gegevens_id' => $contact->id,
                    'adres_id' => $adresId,
                    'voornaam' => $validated['voornaam'],
                    'achternaam' => $validated['achternaam'],
                    'volledig_naam' => GebruikerModel::bouwVolledigNaam(
                        $validated['voornaam'],
                        null,
                        $validated['achternaam']
                    ),
                ]);

                Klant::query()->create([
                    'user_id' => null,
                    'gebruiker_id' => $gebruiker->id,
                ]);
            });

            return redirect()->route('eigenaar.klanten.index')
                ->with('success', 'Klant is toegevoegd.');
        } catch (\Throwable $exception) {
            TechnischeLogModel::registreer('error', 'klanten', 'store', $exception->getMessage());

            return back()->withInput()->withErrors([
                'general' => 'Klant kon niet worden toegevoegd.',
            ]);
        }
    }

    /**
     * GET /eigenaar/klanten/{klant}/bewerken — Formulier om klantgegevens te wijzigen.
     */
    public function edit(int $klant): View|RedirectResponse
    {
        try {
            $klantModel = Klant::query()
                ->with(['user', 'gebruiker.contactGegevens', 'gebruiker.adres'])
                ->findOrFail($klant);

            $gebruiker = $klantModel->gebruiker;
            $contactGegevens = $gebruiker?->contactGegevens;
            $adres = $gebruiker?->adres;

            return view('eigenaar.klant.edit.view', [
                'klant' => $klantModel,
                'form' => [
                    'voornaam' => old('voornaam', $gebruiker?->voornaam),
                    'achternaam' => old('achternaam', $gebruiker?->achternaam),
                    'email' => old('email', $contactGegevens?->email ?? $klantModel->email),
                    'telefoon' => old('telefoon', $contactGegevens?->telefoon),
                    'straat' => old('straat', $adres?->straat),
                    'huisnummer' => old('huisnummer', $adres?->huisnummer),
                    'postcode' => old('postcode', $adres?->postcode),
                    'plaats' => old('plaats', $adres?->plaats),
                    'land' => old('land', $adres?->land ?? 'Nederland'),
                ],
            ]);
        } catch (\Throwable $exception) {
            TechnischeLogModel::registreer('error', 'klanten', 'edit', $exception->getMessage());

            return redirect()->route('eigenaar.klanten.index')
                ->with('error', 'Klantgegevens konden niet worden geladen.');
        }
    }

    /**
     * PUT /eigenaar/klanten/{klant} — Werk klantgegevens bij.
     */
    public function update(Request $request, int $klant): RedirectResponse
    {
        try {
            $klantModel = Klant::query()
                ->with(['user', 'gebruiker.contactGegevens', 'gebruiker.adres'])
                ->findOrFail($klant);

            $gebruiker = $klantModel->gebruiker;
            $contactGegevens = $gebruiker?->contactGegevens;

            if (! $gebruiker) {
                return redirect()->route('eigenaar.klanten.index')
                    ->with('error', 'Klantprofiel is onvolledig en kan niet worden bijgewerkt.');
            }

            $emailRegels = [
                'required',
                'email',
                'max:254',
                Rule::unique('contact_gegevens', 'email')->ignore($contactGegevens?->id),
            ];

            if ($klantModel->user_id) {
                $emailRegels[] = Rule::unique('users', 'email')->ignore($klantModel->user_id);
            }

            $validated = $request->validate([
                'voornaam' => ['required', 'string', 'max:50', 'regex:/^[\pL\s\-\'\.]+$/u'],
                'achternaam' => ['required', 'string', 'max:50', 'regex:/^[\pL\s\-\'\.]+$/u'],
                'email' => $emailRegels,
                'telefoon' => ['nullable', 'string', 'max:25', 'regex:/^(?:\+31|0031|0)[1-9][0-9]{8}$/'],
                'straat' => ['nullable', 'string', 'max:100', 'required_with:huisnummer,postcode,plaats'],
                'huisnummer' => ['nullable', 'string', 'max:10', 'required_with:straat,postcode,plaats'],
                'postcode' => ['nullable', 'string', 'max:10', 'required_with:straat,huisnummer,plaats', 'regex:/^[1-9][0-9]{3}\s?[A-Za-z]{2}$/'],
                'plaats' => ['nullable', 'string', 'max:50', 'required_with:straat,huisnummer,postcode'],
                'land' => ['nullable', 'string', 'max:50'],
            ]);

            DB::transaction(function () use ($klantModel, $gebruiker, $contactGegevens, $validated): void {
                $volledigeNaam = trim($validated['voornaam'] . ' ' . $validated['achternaam']);

                $gebruiker->forceFill([
                    'voornaam' => $validated['voornaam'],
                    'achternaam' => $validated['achternaam'],
                    'volledig_naam' => $volledigeNaam,
                ])->save();

                if ($contactGegevens) {
                    $contactGegevens->forceFill([
                        'email' => $validated['email'],
                        'telefoon' => $validated['telefoon'] ?: null,
                    ])->save();
                } else {
                    $nieuwContact = ContactGegevensModel::query()->create([
                        'email' => $validated['email'],
                        'telefoon' => $validated['telefoon'] ?: null,
                    ]);

                    $gebruiker->contact_gegevens_id = $nieuwContact->id;
                    $gebruiker->save();
                }

                if ($klantModel->user_id) {
                    User::query()->whereKey($klantModel->user_id)->update([
                        'voornaam' => $validated['voornaam'],
                        'achternaam' => $validated['achternaam'],
                        'name' => $volledigeNaam,
                        'email' => $validated['email'],
                    ]);
                }

                $heeftAdresData = collect([
                    $validated['straat'] ?? null,
                    $validated['huisnummer'] ?? null,
                    $validated['postcode'] ?? null,
                    $validated['plaats'] ?? null,
                ])->filter(static fn ($waarde) => filled($waarde))->isNotEmpty();

                $adres = $gebruiker->adres;

                if ($heeftAdresData) {
                    if ($adres) {
                        $adres->forceFill([
                            'straat' => $validated['straat'],
                            'huisnummer' => $validated['huisnummer'],
                            'postcode' => $validated['postcode'],
                            'plaats' => $validated['plaats'],
                            'land' => $validated['land'] ?: 'Nederland',
                        ])->save();
                    } else {
                        $nieuwAdres = AdresModel::query()->create([
                            'straat' => $validated['straat'],
                            'huisnummer' => $validated['huisnummer'],
                            'postcode' => $validated['postcode'],
                            'plaats' => $validated['plaats'],
                            'land' => $validated['land'] ?: 'Nederland',
                        ]);

                        $gebruiker->adres_id = $nieuwAdres->id;
                        $gebruiker->save();
                    }
                }
            });

            return redirect()->route('eigenaar.klanten.index')
                ->with('success', 'Klantgegevens zijn bijgewerkt.');
        } catch (\Throwable $exception) {
            TechnischeLogModel::registreer('error', 'klanten', 'update', $exception->getMessage());

            return back()->withInput()->withErrors([
                'general' => 'Klantgegevens konden niet worden bijgewerkt.',
            ]);
        }
    }

    /**
     * DELETE /eigenaar/klanten/{klant} — Verwijdert een klant en gekoppelde profielgegevens.
     */
    public function destroy(int $klant): RedirectResponse
    {
        try {
            $klantModel = Klant::query()
                ->with(['user', 'gebruiker.contactGegevens', 'gebruiker.adres'])
                ->findOrFail($klant);

            DB::transaction(function () use ($klantModel): void {
                $user = $klantModel->user;
                $gebruiker = $klantModel->gebruiker;
                $contactGegevensId = $gebruiker?->contact_gegevens_id;
                $adresId = $gebruiker?->adres_id;

                if ($user) {
                    $user->delete();
                } else {
                    $klantModel->delete();
                }

                if ($gebruiker) {
                    $gebruiker->delete();
                }

                if ($contactGegevensId
                    && ! GebruikerModel::query()->where('contact_gegevens_id', $contactGegevensId)->exists()) {
                    ContactGegevensModel::query()->whereKey($contactGegevensId)->delete();
                }

                if ($adresId
                    && ! GebruikerModel::query()->where('adres_id', $adresId)->exists()) {
                    AdresModel::query()->whereKey($adresId)->delete();
                }
            });

            return redirect()->route('eigenaar.klanten.index')
                ->with('success', 'Klant is verwijderd.');
        } catch (\Throwable $exception) {
            TechnischeLogModel::registreer('error', 'klanten', 'destroy', $exception->getMessage());

            return redirect()->route('eigenaar.klanten.index')
                ->with('error', 'Klant kon niet worden verwijderd.');
        }
    }
}
