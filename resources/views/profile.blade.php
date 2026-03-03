@extends('layouts.app')

@section('title', 'Mijn profiel')

@section('content')
<div class="page-header">
    <div class="container">
        <h1><i class="fas fa-user-circle me-3"></i>Mijn profiel</h1>
        <p class="mb-0 mt-2 opacity-90">Uw accountgegevens</p>
    </div>
</div>

<div class="container">
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    <div class="row justify-content-center">
        <div class="col-lg-6">
            <div class="card">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center mb-4">
                        <div class="avatar-circle me-3" style="width: 64px; height: 64px; background: linear-gradient(135deg, var(--primary-color), var(--primary-dark)); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-size: 1.5rem; font-weight: 600;">
                            {{ strtoupper(substr(Auth::user()->name ?? 'U', 0, 1)) }}
                        </div>
                        <div>
                            <h4 class="mb-1">{{ Auth::user()->name }}</h4>
                            <span class="badge bg-primary">{{ \App\Models\User::roleLabel(Auth::user()->role) }}</span>
                        </div>
                    </div>
                    <p class="mb-1"><strong><i class="fas fa-envelope me-2"></i>E-mail:</strong> {{ Auth::user()->email }}</p>
                    <p class="mb-0"><strong><i class="fas fa-info-circle me-2"></i>Status:</strong> {{ ucfirst(Auth::user()->status ?? 'Actief') }}</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
