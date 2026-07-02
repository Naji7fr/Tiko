{{-- account.view — Nieuw beheeraccount --}}
@extends('layouts.app')

@section('title', 'Account aanmaken')

@section('content')
<div class="page-header">
    <div class="container">
        <h1><i class="fas fa-user-plus me-3"></i>Account aanmaken</h1>
    </div>
</div>

<div class="container">
    @include('eigenaar.partials.alerts')
    @include('partials.validation-error-modal')

    <form method="POST" action="{{ route('eigenaar.accounts.store') }}">
        @csrf
        @include('eigenaar.account.partials.form')
        <button type="submit" class="btn btn-primary"><i class="fas fa-save me-2"></i>Account aanmaken</button>
        <a href="{{ route('eigenaar.accounts.index') }}" class="btn btn-secondary">Annuleren</a>
    </form>
</div>
@endsection
