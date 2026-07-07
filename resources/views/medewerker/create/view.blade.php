{{-- medewerker.view — Medewerker aanmaken --}}
@extends('layouts.app')

@section('title', 'Medewerker toevoegen')

@push('scripts')
    <script src="{{ asset('js/form-validatie.js') }}" defer></script>
    <script src="{{ asset('js/medewerker/medewerker.validation.js') }}" defer></script>
@endpush

@section('content')
<div class="page-header">
    <div class="container">
        <h1><i class="fas fa-user-plus me-3"></i>Medewerker toevoegen</h1>
    </div>
</div>

<div class="container">
    @include('medewerker.partials.alerts')

    <form method="POST"
          action="{{ route('medewerkers.store') }}"
          id="medewerker-form"
          novalidate>
        @csrf
        @include('medewerker.partials.form')
        <button type="submit" class="btn btn-primary"><i class="fas fa-plus me-2"></i>Toevoegen</button>
        <a href="{{ route('medewerkers.index') }}" class="btn btn-secondary">Annuleren</a>
    </form>
</div>
@endsection
