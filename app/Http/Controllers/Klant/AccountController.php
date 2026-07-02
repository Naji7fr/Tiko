<?php

namespace App\Http\Controllers\Klant;

use App\Http\Controllers\Controller;
use App\Models\Klant;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;
use Illuminate\Validation\Rule;

class AccountController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function details(Request $request): View
    {
        $user = $request->user();
        $klant = $user->klant()->with(['gebruiker.contactGegevens', 'gebruiker.adres'])->first();

        return view('klant.account.details', [
            'user' => $user,
            'klant' => $klant,
        ]);
    }

    public function edit(Request $request): View
    {
        $user = $request->user();
        $klant = $user->klant()->with(['gebruiker.contactGegevens', 'gebruiker.adres'])->first();
        $gebruiker = $klant?->gebruiker;
        $contactGegevens = $gebruiker?->contactGegevens;
        $adres = $gebruiker?->adres;

        return view('klant.account.edit', [
            'user' => $user,
            'klant' => $klant,
            'form' => [
                'voornaam' => old('voornaam', $user->voornaam),
                'achternaam' => old('achternaam', $user->achternaam),
                'email' => old('email', $contactGegevens?->email ?? $user->email),
                'telefoon' => old('telefoon', $contactGegevens?->telefoon),
                'straat' => old('straat', $adres?->straat),
                'huisnummer' => old('huisnummer', $adres?->huisnummer),
                'postcode' => old('postcode', $adres?->postcode),
                'plaats' => old('plaats', $adres?->plaats),
                'land' => old('land', $adres?->land ?? 'Nederland'),
            ],
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        try {
            $user = $request->user();
            $klant = $user->klant()->with(['gebruiker.contactGegevens', 'gebruiker.adres'])->first();
            $gebruiker = $klant?->gebruiker;
            $contactGegevens = $gebruiker?->contactGegevens;
            $adres = $gebruiker?->adres;

            $emailRules = [
                'required',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($user->id),
            ];

            if ($contactGegevens) {
                $emailRules[] = Rule::unique('contact_gegevens', 'email')->ignore($contactGegevens->id);
            }

            $validated = $request->validate([
                'voornaam' => ['required', 'string', 'max:255'],
                'achternaam' => ['required', 'string', 'max:255'],
                'email' => $emailRules,
                'telefoon' => ['nullable', 'string', 'max:25'],
                'straat' => ['nullable', 'string', 'max:100'],
                'huisnummer' => ['nullable', 'string', 'max:10'],
                'postcode' => ['nullable', 'string', 'max:10'],
                'plaats' => ['nullable', 'string', 'max:50'],
                'land' => ['nullable', 'string', 'max:50'],
            ]);

            $user->forceFill([
                'voornaam' => $validated['voornaam'],
                'achternaam' => $validated['achternaam'],
                'name' => trim($validated['voornaam'] . ' ' . $validated['achternaam']),
                'email' => $validated['email'],
            ])->save();

            if ($gebruiker) {
                $gebruiker->forceFill([
                    'voornaam' => $validated['voornaam'],
                    'achternaam' => $validated['achternaam'],
                    'volledig_naam' => trim($validated['voornaam'] . ' ' . $validated['achternaam']),
                ])->save();
            }

            if ($contactGegevens) {
                $contactGegevens->forceFill([
                    'email' => $validated['email'],
                    'telefoon' => $validated['telefoon'],
                ])->save();
            }

            if ($adres) {
                $adres->forceFill([
                    'straat' => $validated['straat'],
                    'huisnummer' => $validated['huisnummer'],
                    'postcode' => $validated['postcode'],
                    'plaats' => $validated['plaats'],
                    'land' => $validated['land'] ?: 'Nederland',
                ])->save();
            }

            return redirect()
                ->route('account.details')
                ->with('status', 'Account details zijn bijgewerkt.');
        } catch (Throwable $exception) {
            Log::error('Account details bijwerken mislukt.', [
                'user_id' => $request->user()?->id,
                'exception' => $exception->getMessage(),
            ]);

            return back()
                ->withInput()
                ->withErrors([
                    'general' => 'De accountgegevens konden niet worden bijgewerkt. Probeer het opnieuw.',
                ]);
        }
    }

    public function settings(Request $request): RedirectResponse
    {
        return redirect()->route('account.details');
    }

    public function destroy(Request $request): RedirectResponse
    {
        abort_unless($request->user()?->isKlant(), 403);

        try {
            $user = $request->user();
            $klant = Klant::with(['gebruiker.contactGegevens', 'gebruiker.adres'])
                ->where('user_id', $user->id)
                ->first();

            DB::transaction(function () use ($user, $klant): void {
                $gebruiker = $klant?->gebruiker;
                $adres = $gebruiker?->adres;

                if ($adres) {
                    $adres->delete();
                }

                if ($gebruiker) {
                    $gebruiker->delete();
                }

                $user->delete();
            });

            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()
                ->route('home')
                ->with('status', 'Je account is verwijderd.');
        } catch (Throwable $exception) {
            Log::error('Account verwijderen mislukt.', [
                'user_id' => $request->user()?->id,
                'exception' => $exception->getMessage(),
            ]);

            return back()->withErrors([
                'general' => 'Je account kon niet worden verwijderd. Probeer het opnieuw.',
            ]);
        }
    }
}