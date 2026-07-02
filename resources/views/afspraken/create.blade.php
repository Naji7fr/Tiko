@extends('layouts.app')

@section('title', 'Nieuwe afspraak')

@section('content')
<div class="page-header">
    <div class="container">
        <h1><i class="fas fa-calendar-plus me-3"></i>Nieuwe afspraak</h1>
        <p class="mb-0 mt-2 opacity-90">Kies klant, behandeling, specialist, datum en tijd.</p>
    </div>
</div>

<div class="container">
    @include('medewerker.partials.alerts')

    <div class="card shadow-sm">
        <div class="card-body">
            <form action="{{ route('afspraken.store') }}" method="POST" class="row g-3">
                @csrf

                @if($eigenKlant)
                    <div class="col-12">
                        <label class="form-label">Klant</label>
                        <input type="text" class="form-control" readonly
                               value="{{ $eigenKlant->gebruiker?->volledig_naam ?? auth()->user()->name }}">
                        <small class="text-muted">Je plant een afspraak voor jezelf.</small>
                    </div>
                @else
                    <div class="col-12">
                        <label for="klant_id" class="form-label">Klant</label>
                        <select name="klant_id" id="klant_id" class="form-select @error('klant_id') is-invalid @enderror" required>
                            <option value="">Kies een klant</option>
                            @foreach($klanten as $klant)
                                <option value="{{ $klant->id }}" {{ (string) old('klant_id') === (string) $klant->id ? 'selected' : '' }}>
                                    {{ $klant->gebruiker?->volledig_naam ?? 'Onbekend' }}
                                </option>
                            @endforeach
                        </select>
                        @error('klant_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                @endif

                <div class="col-md-6">
                    <label for="behandeling_id" class="form-label">Behandeling</label>
                    <select name="behandeling_id" id="behandeling_id" class="form-select" required>
                        <option value="">Kies een behandeling</option>
                        @foreach($behandelingen as $behandeling)
                            <option value="{{ $behandeling->id }}" {{ old('behandeling_id') == $behandeling->id ? 'selected' : '' }}>{{ $behandeling->naam }} ({{ $behandeling->duur_minuten }} min)</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-6">
                    <label for="medewerker_id" class="form-label">Specialist</label>
                    <select name="medewerker_id" id="medewerker_id" class="form-select" required>
                        <option value="">Kies een specialist</option>
                        @foreach($medewerkers as $medewerker)
                            <option value="{{ $medewerker->id }}" {{ old('medewerker_id') == $medewerker->id ? 'selected' : '' }}>
                                {{ $medewerker->gebruiker?->volledig_naam ?? 'Onbekend' }}
                                @if($medewerker->specialisatie?->naam)
                                    ({{ $medewerker->specialisatie->naam }})
                                @endif
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-6">
                    <label for="afspraak_datum" class="form-label">Datum</label>
                    <input type="date" name="afspraak_datum" id="afspraak_datum" class="form-control" min="{{ now()->toDateString() }}" value="{{ old('afspraak_datum', now()->toDateString()) }}" required>
                </div>

                <div class="col-md-6">
                    <label for="afspraak_tijd" class="form-label">Starttijd</label>
                    <input type="time" name="afspraak_tijd" id="afspraak_tijd" class="form-control" min="09:00" max="19:59" value="{{ old('afspraak_tijd') }}" required>
                </div>

                <div class="col-12">
                    <label for="opmerking" class="form-label">Opmerking</label>
                    <textarea name="opmerking" id="opmerking" rows="3" class="form-control" maxlength="225">{{ old('opmerking') }}</textarea>
                </div>

                <div class="col-12 d-flex gap-2">
                    <button type="submit" class="btn btn-primary">Afspraak bevestigen</button>
                    @if(auth()->user()?->isKlant())
                        <a href="{{ route('klant.dashboard') }}" class="btn btn-outline-secondary">Annuleren</a>
                    @else
                        <a href="{{ route('afspraken.index') }}" class="btn btn-outline-secondary">Annuleren</a>
                    @endif
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
