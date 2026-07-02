<?php

namespace App\Http\Controllers\Eigenaar;

use App\Http\Controllers\Controller;
use App\Http\Requests\Eigenaar\StoreAccountRequest;
use App\Http\Requests\Eigenaar\UpdateAccountRequest;
use App\Models\Medewerker\TechnischeLogModel;
use App\Models\User;
use App\Services\Eigenaar\AccountService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

/**
 * account.controller — Beheer loginaccounts (eigenaar / medewerker / klant).
 */
class AccountController extends Controller
{
    public function __construct(
        private readonly AccountService $accountService
    ) {}

    /** GET /eigenaar/accounts — Overzicht beheeraccounts. */
    public function index(): View|RedirectResponse
    {
        try {
            $accounts = $this->accountService->haalAccountsOp();

            return view('eigenaar.account.index.view', compact('accounts'));
        } catch (\Throwable $exception) {
            TechnischeLogModel::registreer('error', 'account', 'index', $exception->getMessage());

            return redirect()->route('eigenaar.dashboard')
                ->with('error', 'Accounts konden niet worden geladen.');
        }
    }

    /** GET /eigenaar/accounts/create — Formulier nieuw account. */
    public function create(): View
    {
        return view('eigenaar.account.create.view');
    }

    /** POST /eigenaar/accounts — Account opslaan. */
    public function store(StoreAccountRequest $request): RedirectResponse
    {
        try {
            $this->accountService->voegAccountToe($request->validated());

            return redirect()
                ->route('eigenaar.accounts.index')
                ->with('success', 'Account succesvol aangemaakt!');
        } catch (\Throwable $exception) {
            TechnischeLogModel::registreer('error', 'account', 'store', $exception->getMessage());

            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Account kon niet worden aangemaakt.');
        }
    }

    /** GET /eigenaar/accounts/{account}/edit — Wijzigformulier. */
    public function edit(User $account): View|RedirectResponse
    {
        if (! in_array($account->role, AccountService::BEHEER_ROLLEN, true)) {
            return redirect()
                ->route('eigenaar.accounts.index')
                ->with('error', 'Dit account kan niet worden beheerd.');
        }

        $account->load('klant.gebruiker.contactGegevens');
        $telefoon = $account->klant?->gebruiker?->contactGegevens?->telefoon;

        return view('eigenaar.account.edit.view', compact('account', 'telefoon'));
    }

    /** PUT /eigenaar/accounts/{account} — Account bijwerken. */
    public function update(UpdateAccountRequest $request, User $account): RedirectResponse
    {
        if (! in_array($account->role, AccountService::BEHEER_ROLLEN, true)) {
            return redirect()
                ->route('eigenaar.accounts.index')
                ->with('error', 'Dit account kan niet worden beheerd.');
        }

        // Eigenaar kan zichzelf niet deactiveerden of demoten
        if ($account->id === Auth::id()) {
            if ($request->input('status') !== 'Actief' || $request->input('role') !== 'admin') {
                return redirect()
                    ->back()
                    ->withInput()
                    ->with('error', 'Je kunt je eigen rol of status niet wijzigen.');
            }
        }

        try {
            $this->accountService->wijzigAccount($account, $request->validated());

            return redirect()
                ->route('eigenaar.accounts.index')
                ->with('success', 'Account succesvol gewijzigd!');
        } catch (\Throwable $exception) {
            TechnischeLogModel::registreer('error', 'account', 'update', $exception->getMessage());

            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Account kon niet worden gewijzigd.');
        }
    }

    /** DELETE /eigenaar/accounts/{account} — Account verwijderen. */
    public function destroy(User $account): RedirectResponse
    {
        if (! in_array($account->role, AccountService::BEHEER_ROLLEN, true)) {
            return redirect()
                ->route('eigenaar.accounts.index')
                ->with('error', 'Dit account kan niet worden verwijderd.');
        }

        if ($account->id === Auth::id()) {
            return redirect()
                ->route('eigenaar.accounts.index')
                ->with('error', 'Je kunt je eigen account niet verwijderen.');
        }

        if ($account->role === 'admin' && User::where('role', 'admin')->count() <= 1) {
            return redirect()
                ->route('eigenaar.accounts.index')
                ->with('error', 'De laatste eigenaar kan niet worden verwijderd.');
        }

        try {
            $this->accountService->verwijderAccount($account);

            return redirect()
                ->route('eigenaar.accounts.index')
                ->with('success', 'Account succesvol verwijderd!');
        } catch (\Throwable $exception) {
            TechnischeLogModel::registreer('error', 'account', 'destroy', $exception->getMessage());

            return redirect()
                ->route('eigenaar.accounts.index')
                ->with('error', 'Account kon niet worden verwijderd.');
        }
    }
}
