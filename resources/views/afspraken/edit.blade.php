@extends('layouts.app')

@section('title', 'Afspraak wijzigen')

@push('scripts')
    <script src="{{ asset('js/afspraken/afspraak-planning.js') }}" defer></script>
@endpush

@section('content')
<div class="page-header">
    <div class="container">
        <h1><i class="fas fa-calendar-check me-3"></i>Afspraak wijzigen</h1>
        <p class="mb-0 mt-2 opacity-90">Pas behandeling, specialist, datum of starttijd aan.</p>
    </div>
</div>

<div class="container">
    @include('medewerker.partials.alerts')

    <div class="card shadow-sm">
        <div class="card-body">
            <form action="{{ route('afspraken.update', $afspraak) }}" method="POST" class="row g-3">
                @csrf
                @method('PUT')

                <div class="col-12">
                    <label for="klant_id" class="form-label">Klant</label>
                    <select name="klant_id" id="klant_id" class="form-select @error('klant_id') is-invalid @enderror" required>
                        @foreach($klanten as $klant)
                            <option value="{{ $klant->id }}" {{ (string) old('klant_id', $afspraak->klant_id) === (string) $klant->id ? 'selected' : '' }}>
                                {{ $klant->gebruiker?->volledig_naam ?? 'Onbekend' }}
                            </option>
                        @endforeach
                    </select>
                    @error('klant_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                @include('afspraken.partials.planning-form')

                <div class="col-12">
                    <label for="opmerking" class="form-label">Opmerking</label>
                    <textarea name="opmerking" id="opmerking" rows="3" class="form-control" maxlength="225">{{ old('opmerking', $afspraak->opmerking) }}</textarea>
                </div>

                <div class="col-12 d-flex gap-2">
                    <button type="submit" class="btn btn-primary">Wijzigingen opslaan</button>
                    <a href="{{ route('afspraken.index') }}" class="btn btn-outline-secondary">Annuleren</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
