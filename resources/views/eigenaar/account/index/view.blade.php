{{-- account.view — Overzicht beheeraccounts --}}
@extends('layouts.app')

@section('title', 'Accounts beheren')

@section('content')
<div class="page-header">
    <div class="container">
        <h1><i class="fas fa-user-shield me-3"></i>Accounts</h1>
        <p class="mb-0 mt-2 opacity-90">Beheer loginaccounts voor eigenaar, medewerkers en klanten</p>
    </div>
</div>

<div class="container">
    @include('eigenaar.partials.alerts')
    @include('partials.validation-error-modal')

    <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-3 mb-4 page-toolbar">
        <div>
            <h2 class="mb-0" style="color: var(--text-primary);">Alle accounts</h2>
            <p class="text-muted mb-0">Totaal: {{ $accounts->count() }} accounts</p>
        </div>
        <a href="{{ route('eigenaar.accounts.create') }}" class="btn btn-primary w-100 w-sm-auto">
            <i class="fas fa-plus me-2"></i>Account aanmaken
        </a>
    </div>

    @if($accounts->isEmpty())
        <div class="card">
            <div class="card-body text-center py-5">
                <i class="fas fa-user-shield fa-3x text-muted mb-3"></i>
                <h4 class="text-muted">Geen accounts gevonden</h4>
                <a href="{{ route('eigenaar.accounts.create') }}" class="btn btn-primary mt-3">
                    <i class="fas fa-plus me-2"></i>Account aanmaken
                </a>
            </div>
        </div>
    @else
        <div class="card">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th>Naam</th>
                            <th>E-mail</th>
                            <th>Gebruikersnaam</th>
                            <th>Rol</th>
                            <th>Status</th>
                            <th class="text-end">Acties</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($accounts as $account)
                            <tr>
                                <td><strong>{{ trim(($account->voornaam ?? '') . ' ' . ($account->achternaam ?? '')) ?: $account->name }}</strong></td>
                                <td>{{ $account->email }}</td>
                                <td><code>{{ $account->name }}</code></td>
                                <td>
                                    @if($account->role === 'admin')
                                        <span class="badge bg-primary"><i class="fas fa-crown me-1"></i>Eigenaar</span>
                                    @elseif($account->role === 'medewerker')
                                        <span class="badge bg-info text-dark"><i class="fas fa-user-tie me-1"></i>Medewerker</span>
                                    @else
                                        <span class="badge bg-secondary"><i class="fas fa-user me-1"></i>Klant</span>
                                    @endif
                                </td>
                                <td>
                                    @if(strtolower($account->status) === 'actief')
                                        <span class="badge bg-success">Actief</span>
                                    @else
                                        <span class="badge bg-secondary">{{ $account->status }}</span>
                                    @endif
                                </td>
                                <td class="text-end">
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('eigenaar.accounts.edit', $account) }}" class="btn btn-sm btn-warning">
                                            <i class="fas fa-edit me-1"></i>Wijzigen
                                        </a>
                                        @if($account->id !== auth()->id())
                                            <button type="button"
                                                    class="btn btn-sm btn-danger"
                                                    data-delete-trigger
                                                    data-delete-url="{{ route('eigenaar.accounts.destroy', $account) }}"
                                                    data-delete-name="{{ $account->email }}">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif
</div>
@endsection
