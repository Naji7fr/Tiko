@extends('layouts.app')

@section('title', 'Overzicht Accounts')

@section('content')
<div class="page-header">
    <div class="container">
        <h1><i class="fas fa-user-shield me-3"></i>Overzicht Accounts</h1>
        <p class="mb-0 mt-2 opacity-90">Beheer alle gebruikersaccounts en rechten</p>
    </div>
</div>

<div class="container">
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-0" style="color: var(--text-primary);">Alle Accounts</h2>
            <p class="text-muted mb-0">Totaal: {{ $accounts->count() }} accounts</p>
        </div>
        <a href="{{ route('accounts.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i>Nieuw Account
        </a>
    </div>

    @if($accounts->isEmpty())
        <div class="card">
            <div class="card-body text-center py-5">
                <i class="fas fa-user-shield fa-3x text-muted mb-3"></i>
                <h4 class="text-muted">Geen accounts gevonden</h4>
                <p class="text-muted mb-4">Er zijn momenteel geen accounts beschikbaar in het systeem.</p>
                <a href="{{ route('accounts.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus me-2"></i>Voeg eerste account toe
                </a>
            </div>
        </div>
    @else
        <div class="card">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th><i class="fas fa-user me-2"></i>Naam</th>
                            <th><i class="fas fa-envelope me-2"></i>Email</th>
                            <th><i class="fas fa-user-tag me-2"></i>Rol</th>
                            <th><i class="fas fa-info-circle me-2"></i>Status</th>
                            <th><i class="fas fa-calendar me-2"></i>Aanmaakdatum</th>
                            <th class="text-end"><i class="fas fa-cog me-2"></i>Acties</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($accounts as $account)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar-circle me-3" style="width: 40px; height: 40px; background: linear-gradient(135deg, var(--primary-color), var(--primary-dark)); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-weight: 600;">
                                            {{ strtoupper(substr($account->name, 0, 1)) }}
                                        </div>
                                        <strong>{{ $account->name }}</strong>
                                    </div>
                                </td>
                                <td>
                                    <a href="mailto:{{ $account->email }}" class="text-decoration-none">
                                        <i class="fas fa-envelope me-2 text-muted"></i>{{ $account->email }}
                                    </a>
                                </td>
                                <td>
                                    @php
                                        $roleColors = [
                                            'admin' => 'danger',
                                            'manager' => 'warning',
                                            'financieel_medewerker' => 'primary',
                                            'reisadviseur' => 'info',
                                            'klant' => 'success',
                                        ];
                                        $roleLabels = [
                                            'admin' => 'Admin',
                                            'manager' => 'Manager',
                                            'financieel_medewerker' => 'Financieel Medewerker',
                                            'reisadviseur' => 'Reisadviseur',
                                            'klant' => 'Klant',
                                        ];
                                        $roleColor = $roleColors[$account->role] ?? 'secondary';
                                        $roleLabel = $roleLabels[$account->role] ?? ucfirst($account->role);
                                    @endphp
                                    <span class="badge bg-{{ $roleColor }}">
                                        <i class="fas fa-user-tag me-1"></i>{{ $roleLabel }}
                                    </span>
                                </td>
                                <td>
                                    @if($account->status == 'actief')
                                        <span class="badge bg-success">
                                            <i class="fas fa-check-circle me-1"></i>Actief
                                        </span>
                                    @elseif($account->status == 'inactief')
                                        <span class="badge bg-secondary">
                                            <i class="fas fa-pause-circle me-1"></i>Inactief
                                        </span>
                                    @else
                                        <span class="badge bg-warning">
                                            <i class="fas fa-exclamation-circle me-1"></i>{{ ucfirst($account->status) }}
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    <i class="fas fa-calendar me-2 text-muted"></i>
                                    {{ $account->created_at->format('d-m-Y') }}
                                </td>
                                <td class="text-end">
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('accounts.edit', $account->id) }}" class="btn btn-sm btn-warning" title="Bewerken">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('accounts.destroy', $account->id) }}" method="POST" style="display:inline-block;" onsubmit="return confirm('Weet je zeker dat je dit account wilt verwijderen?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger" title="Verwijderen">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
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