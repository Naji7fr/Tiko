@extends('layouts.app')

@section('title', 'Account bekijken')

@section('content')
<div class="page-header">
    <div class="container">
        <h1><i class="fas fa-user-shield me-3"></i>Account details</h1>
        <p class="mb-0 mt-2 opacity-90">{{ $account->name }}</p>
    </div>
</div>

<div class="container">
    <div class="card">
        <div class="card-body p-4">
            <div class="d-flex align-items-center mb-4">
                <div class="avatar-circle me-3" style="width: 64px; height: 64px; background: linear-gradient(135deg, var(--primary-color), var(--primary-dark)); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-size: 1.5rem; font-weight: 600;">
                    {{ strtoupper(substr($account->name, 0, 1)) }}
                </div>
                <div>
                    <h4 class="mb-1">{{ $account->name }}</h4>
                    <span class="badge bg-primary">{{ \App\Models\User::roleLabel($account->role) }}</span>
                </div>
            </div>
            @if($account->voornaam || $account->achternaam)
            <p class="mb-2"><strong><i class="fas fa-id-card me-2"></i>Naam:</strong> {{ trim(($account->voornaam ?? '') . ' ' . ($account->achternaam ?? '')) }}</p>
            @endif
            <p class="mb-2"><strong><i class="fas fa-envelope me-2"></i>E-mail:</strong> {{ $account->email }}</p>
            <p class="mb-2"><strong><i class="fas fa-info-circle me-2"></i>Status:</strong> {{ ucfirst($account->status ?? 'Onbekend') }}</p>
            <p class="mb-0"><strong><i class="fas fa-calendar me-2"></i>Aanmaakdatum:</strong> {{ $account->created_at->format('d-m-Y H:i') }}</p>
            <hr class="my-4">
            <a href="{{ route('accounts.edit', $account->id) }}" class="btn btn-warning"><i class="fas fa-edit me-2"></i>Bewerken</a>
            <a href="{{ route('accounts.index') }}" class="btn btn-secondary"><i class="fas fa-arrow-left me-2"></i>Terug naar overzicht</a>
        </div>
    </div>
</div>
@endsection
