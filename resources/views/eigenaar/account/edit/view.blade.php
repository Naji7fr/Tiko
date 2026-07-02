{{-- account.view — Account wijzigen --}}
@extends('layouts.app')

@section('title', 'Account wijzigen')

@section('content')
<div class="page-header">
    <div class="container">
        <h1><i class="fas fa-edit me-3"></i>Account wijzigen</h1>
        <p class="mb-0 mt-2 opacity-90">{{ $account->email }}</p>
    </div>
</div>

<div class="container">
    @include('eigenaar.partials.alerts')
    @include('partials.validation-error-modal')

    @if($account->id === auth()->id())
        <div class="alert alert-info">
            <i class="fas fa-info-circle me-2"></i>Je bewerkt je eigen account. Rol en status kunnen niet worden gewijzigd.
        </div>
    @endif

    <form method="POST" action="{{ route('eigenaar.accounts.update', $account) }}">
        @csrf
        @method('PUT')
        @include('eigenaar.account.partials.form', ['account' => $account, 'telefoon' => $telefoon ?? null])
        <button type="submit" class="btn btn-primary"><i class="fas fa-save me-2"></i>Opslaan</button>
        <a href="{{ route('eigenaar.accounts.index') }}" class="btn btn-secondary">Annuleren</a>
    </form>
</div>
@endsection
