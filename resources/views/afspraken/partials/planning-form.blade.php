{{-- Gedeelde planningsvelden: behandeling → specialist → datum → starttijd --}}
@php
    $afspraak = $afspraak ?? null;
    $gekozenBehandeling = old('behandeling_id', $afspraak?->behandeling_id ?? '');
    $gekozenMedewerker = old('medewerker_id', $afspraak?->medewerker_id ?? '');
    $gekozenDatum = old('afspraak_datum', $afspraak?->afspraak_datum ?? '');
    $gekozenTijd = old('afspraak_tijd', $afspraak ? substr((string) $afspraak->afspraak_tijd, 0, 5) : '');
    $afspraakId = $afspraak?->id;
@endphp

<div id="afspraak-planning"
     data-medewerkers-url="{{ route('afspraken.beschikbare-medewerkers') }}"
     data-datums-url="{{ route('afspraken.beschikbare-datums') }}"
     data-tijden-url="{{ route('afspraken.beschikbare-tijden') }}"
     data-behavioraling="{{ $gekozenBehandeling }}"
     data-medewerker="{{ $gekozenMedewerker }}"
     data-datum="{{ $gekozenDatum }}"
     data-tijd="{{ $gekozenTijd }}"
     data-afspraak-id="{{ $afspraakId }}"
     class="row g-3">

    <div class="col-md-6">
        <label for="behandeling_id" class="form-label">Behandeling</label>
        <select name="behandeling_id" id="behandeling_id" class="form-select @error('behandeling_id') is-invalid @enderror" required>
            <option value="">Kies een behandeling</option>
            @foreach($behandelingen as $behandeling)
                <option value="{{ $behandeling->id }}" {{ (string) $gekozenBehandeling === (string) $behandeling->id ? 'selected' : '' }}>
                    {{ $behandeling->naam }} ({{ $behandeling->duur_minuten }} min)
                </option>
            @endforeach
        </select>
        @error('behandeling_id')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-6">
        <label for="medewerker_id" class="form-label">Specialist</label>
        <select name="medewerker_id" id="medewerker_id" class="form-select @error('medewerker_id') is-invalid @enderror" required disabled>
            <option value="">Kies eerst een behandeling</option>
        </select>
        @error('medewerker_id')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-6">
        <label for="afspraak_datum" class="form-label">Datum</label>
        <select name="afspraak_datum" id="afspraak_datum" class="form-select @error('afspraak_datum') is-invalid @enderror" required disabled>
            <option value="">Kies eerst een specialist</option>
        </select>
        @error('afspraak_datum')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-6">
        <label for="afspraak_tijd" class="form-label">Starttijd</label>
        <select name="afspraak_tijd" id="afspraak_tijd" class="form-select @error('afspraak_tijd') is-invalid @enderror" required disabled>
            <option value="">Kies eerst een datum</option>
        </select>
        @error('afspraak_tijd')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-12">
        <div id="afspraak-planning-melding" class="alert alert-warning d-none mb-0" role="alert">
            <i class="fas fa-exclamation-triangle me-2"></i>
            <span id="afspraak-planning-melding-tekst"></span>
        </div>
    </div>
</div>
