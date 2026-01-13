@extends('layouts.app')

@section('title', 'Overzicht Berichten')

@section('content')
<div class="page-header">
    <div class="container">
        <h1><i class="fas fa-envelope me-3"></i>Overzicht Berichten</h1>
        <p class="mb-0 mt-2 opacity-90">Beheer alle berichten en communicatie</p>
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
            <h2 class="mb-0" style="color: var(--text-primary);">Alle Berichten</h2>
            <p class="text-muted mb-0">Totaal: {{ $messages->count() }} berichten</p>
        </div>
        <a href="{{ route('berichten.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i>Nieuw Bericht
        </a>
    </div>

    @php
        $unreadCount = $messages->where('is_read', false)->count();
        $readCount = $messages->where('is_read', true)->count();
    @endphp

    <div class="row mb-4">
        <div class="col-md-6 mb-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="avatar-circle" style="width: 50px; height: 50px; background: linear-gradient(135deg, var(--primary-color), var(--primary-dark)); border-radius: 12px; display: flex; align-items: center; justify-content: center; color: white; font-size: 1.5rem;">
                                <i class="fas fa-envelope-open"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="text-muted mb-1">Ongelezen</h6>
                            <h4 class="mb-0 text-primary">{{ $unreadCount }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6 mb-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="avatar-circle" style="width: 50px; height: 50px; background: linear-gradient(135deg, var(--success-color), #059669); border-radius: 12px; display: flex; align-items: center; justify-content: center; color: white; font-size: 1.5rem;">
                                <i class="fas fa-check-double"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="text-muted mb-1">Gelezen</h6>
                            <h4 class="mb-0 text-success">{{ $readCount }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if($messages->isEmpty())
        <div class="card">
            <div class="card-body text-center py-5">
                <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                <h4 class="text-muted">Geen berichten gevonden</h4>
                <p class="text-muted mb-4">Er zijn momenteel geen berichten beschikbaar in het systeem.</p>
                <a href="{{ route('berichten.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus me-2"></i>Voeg eerste bericht toe
                </a>
            </div>
        </div>
    @else
        <div class="card">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th><i class="fas fa-envelope me-2"></i>Onderwerp</th>
                            <th><i class="fas fa-user me-2"></i>Afzender</th>
                            <th><i class="fas fa-user-friends me-2"></i>Ontvanger</th>
                            <th><i class="fas fa-eye me-2"></i>Status</th>
                            <th><i class="fas fa-calendar me-2"></i>Datum</th>
                            <th class="text-end"><i class="fas fa-cog me-2"></i>Acties</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($messages as $message)
                            <tr class="{{ !$message->is_read ? 'table-warning' : '' }}">
                                <td data-label="Onderwerp">
                                    <div class="d-flex align-items-center">
                                        @if(!$message->is_read)
                                            <i class="fas fa-circle text-primary me-2" style="font-size: 0.5rem;"></i>
                                        @endif
                                        <strong>{{ $message->subject }}</strong>
                                    </div>
                                </td>
                                <td data-label="Afzender">
                                    <div class="d-flex align-items-center">
                                        <div class="avatar-circle me-2" style="width: 32px; height: 32px; background: linear-gradient(135deg, var(--primary-color), var(--primary-dark)); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-weight: 600; font-size: 0.75rem;">
                                            {{ strtoupper(substr($message->sender->name ?? 'O', 0, 1)) }}
                                        </div>
                                        {{ $message->sender->name ?? 'Onbekend' }}
                                    </div>
                                </td>
                                <td data-label="Ontvanger">
                                    <div class="d-flex align-items-center">
                                        <div class="avatar-circle me-2" style="width: 32px; height: 32px; background: linear-gradient(135deg, var(--success-color), #059669); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-weight: 600; font-size: 0.75rem;">
                                            {{ strtoupper(substr($message->receiver->name ?? 'A', 0, 1)) }}
                                        </div>
                                        {{ $message->receiver->name ?? 'Allen' }}
                                    </div>
                                </td>
                                <td data-label="Status">
                                    @if($message->is_read)
                                        <span class="badge bg-success">
                                            <i class="fas fa-check-double me-1"></i>Gelezen
                                        </span>
                                    @else
                                        <span class="badge bg-warning">
                                            <i class="fas fa-envelope me-1"></i>Ongelezen
                                        </span>
                                    @endif
                                </td>
                                <td data-label="Datum">
                                    <i class="fas fa-calendar me-2 text-muted"></i>
                                    {{ $message->created_at->format('d-m-Y H:i') }}
                                </td>
                                <td data-label="Acties" class="text-end">
                                    <div class="d-flex gap-2">
                                        <a href="{{ route('berichten.show', $message->id) }}" class="btn btn-sm btn-primary" title="Bekijken">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('berichten.edit', $message->id) }}" class="btn btn-sm btn-warning" title="Bewerken">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('berichten.destroy', $message->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Weet je zeker dat je dit bericht wilt verwijderen?');">
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