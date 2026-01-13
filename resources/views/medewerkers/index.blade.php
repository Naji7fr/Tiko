@extends('layouts.app')

@section('title', 'Medewerker Overzicht')

@section('content')
<div class="page-header">
    <div class="container">
        <h1><i class="fas fa-users me-3"></i>Medewerker Overzicht</h1>
        <p class="mb-0 mt-2 opacity-90">Beheer alle medewerkers van de praktijk</p>
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
            <h2 class="mb-0" style="color: var(--text-primary);">Alle Medewerkers</h2>
            <p class="text-muted mb-0">Totaal: {{ $medewerkers->count() }} medewerkers</p>
        </div>
        <a href="{{ route('medewerkers.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i>Nieuwe Medewerker
        </a>
    </div>

    @if($medewerkers->isEmpty())
        <div class="card">
            <div class="card-body text-center py-5">
                <i class="fas fa-users fa-3x text-muted mb-3"></i>
                <h4 class="text-muted">Geen medewerkers gevonden</h4>
                <p class="text-muted mb-4">Er zijn nog geen medewerkers geregistreerd in het systeem.</p>
                <a href="{{ route('medewerkers.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus me-2"></i>Voeg eerste medewerker toe
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
                            <th><i class="fas fa-info-circle me-2"></i>Status</th>
                            <th><i class="fas fa-tag me-2"></i>Type</th>
                            <th class="text-end"><i class="fas fa-cog me-2"></i>Acties</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($medewerkers as $medewerker)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar-circle me-3" style="width: 40px; height: 40px; background: linear-gradient(135deg, var(--primary-color), var(--primary-dark)); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-weight: 600;">
                                            {{ strtoupper(substr($medewerker->naam, 0, 1)) }}
                                        </div>
                                        <strong>{{ $medewerker->naam }}</strong>
                                    </div>
                                </td>
                                <td>
                                    <a href="mailto:{{ $medewerker->email }}" class="text-decoration-none">
                                        <i class="fas fa-envelope me-2 text-muted"></i>{{ $medewerker->email }}
                                    </a>
                                </td>
                                <td>
                                    @if($medewerker->status == 'actief')
                                        <span class="badge bg-success">
                                            <i class="fas fa-check-circle me-1"></i>Actief
                                        </span>
                                    @else
                                        <span class="badge bg-secondary">
                                            <i class="fas fa-pause-circle me-1"></i>{{ ucfirst($medewerker->status) }}
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge bg-primary">
                                        <i class="fas fa-tag me-1"></i>{{ ucfirst($medewerker->type) }}
                                    </span>
                                </td>
                                <td class="text-end">
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('medewerkers.edit', $medewerker->id) }}" class="btn btn-sm btn-warning" title="Bewerken">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('medewerkers.destroy', $medewerker->id) }}" method="POST" style="display:inline-block;" onsubmit="return confirm('Weet je zeker dat je deze medewerker wilt verwijderen?');">
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
