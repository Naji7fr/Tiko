@extends('layouts.app')

@section('title', 'Afspraak wijzigen')

@section('content')
<div class="page-header">
    <div class="container">
        <h1><i class="fas fa-calendar-check me-3"></i>Afspraak wijzigen</h1>
        <p class="mb-0 mt-2 opacity-90">Pas datum, tijd of specialist aan.</p>
    </div>
</div>

<div class="container">
    @include('medewerker.partials.alerts')

    <div class="card shadow-sm">
        <div class="card-body">
            <form action="{{ route('afspraken.update', $afspraak) }}" method="POST" class="row g-3">
                @csrf
                @method('PUT')

                <div class="col-md-6">
                    <label for="behandeling_id" class="form-label">Behandeling</label>
                    <select name="behandeling_id" id="behandeling_id" class="form-select" required>
                        @foreach($behandelingen as $behandeling)
                            <option value="{{ $behandeling->id }}" {{ old('behandeling_id', $afspraak->behandeling_id) == $behandeling->id ? 'selected' : '' }}>{{ $behandeling->naam }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-6">
                    <label for="medewerker_id" class="form-label">Specialist</label>
                    <select name="medewerker_id" id="medewerker_id" class="form-select" required>
                        @foreach($medewerkers as $medewerker)
                            <option value="{{ $medewerker->id }}" {{ old('medewerker_id', $afspraak->medewerker_id) == $medewerker->id ? 'selected' : '' }}>{{ $medewerker->gebruiker?->volledig_naam ?? 'Onbekend' }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-6">
                    <label for="afspraak_datum" class="form-label">Datum</label>
                    <input type="date" name="afspraak_datum" id="afspraak_datum" class="form-control" min="{{ now()->toDateString() }}" value="{{ old('afspraak_datum', $afspraak->afspraak_datum) }}" required>
                </div>

                <div class="col-md-6">
                    <label for="afspraak_tijd" class="form-label">Starttijd</label>
                    <input type="time" name="afspraak_tijd" id="afspraak_tijd" class="form-control" value="{{ old('afspraak_tijd', $afspraak->afspraak_tijd) }}" required>
                </div>

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
