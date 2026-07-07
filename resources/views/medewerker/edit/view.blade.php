{{-- medewerker.view — Medewerker wijzigen --}}
@extends('layouts.app')

@section('title', 'Medewerker wijzigen')

@push('scripts')
    <script src="{{ asset('js/form-validatie.js') }}" defer></script>
    <script src="{{ asset('js/medewerker/medewerker.validation.js') }}" defer></script>
@endpush

@section('content')
<div class="page-header">
    <div class="container">
        <h1><i class="fas fa-edit me-3"></i>Medewerker wijzigen</h1>
    </div>
</div>

<div class="container">
    @include('medewerker.partials.alerts')

    <form method="POST"
          action="{{ route('medewerkers.update', $medewerker) }}"
          id="medewerker-form"
          novalidate>
        @csrf
        @method('PUT')
        @include('medewerker.partials.form')
        <button type="submit" class="btn btn-primary"><i class="fas fa-save me-2"></i>Opslaan</button>
        <a href="{{ route('medewerkers.index') }}" class="btn btn-secondary">Annuleren</a>
    </form>
</div>
@endsection
