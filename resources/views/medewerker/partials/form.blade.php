{{--
    medewerker.view — Gedeeld formulier (ContactGegevens → Gebruiker → Medewerker).
    Client-side validatie: HTML5 + medewerker.validation.js
    Server-side validatie: StoreMedewerkerRequest / UpdateMedewerkerRequest
--}}

<div class="card mb-4">
    <div class="card-header"><i class="fas fa-envelope me-2"></i>Contactgegevens</div>
    <div class="card-body">
        <div class="mb-3">
            <label for="email" class="form-label">E-mailadres <span class="text-danger">*</span></label>
            <input type="email"
                   class="form-control @error('email') is-invalid @enderror"
                   id="email"
                   name="email"
                   value="{{ old('email', $medewerker->email ?? '') }}"
                   required
                   maxlength="254"
                   autocomplete="email"
                   data-medewerker-validate="email">
            @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
            <div class="invalid-feedback d-none" data-client-error="email"></div>
        </div>
        <div class="mb-0">
            <label for="telefoon" class="form-label">Telefoonnummer</label>
            <input type="tel"
                   class="form-control @error('telefoon') is-invalid @enderror"
                   id="telefoon"
                   name="telefoon"
                   value="{{ old('telefoon', $medewerker->telefoonnummer ?? '') }}"
                   placeholder="0612345678"
                   maxlength="25"
                   pattern="^(?:\+31|0031|0)[1-9][0-9]{8}$"
                   data-medewerker-validate="telefoon">
            @error('telefoon')<div class="invalid-feedback">{{ $message }}</div>@enderror
            <div class="invalid-feedback d-none" data-client-error="telefoon"></div>
            <small class="text-muted">Formaat: 0612345678 of +31612345678</small>
        </div>
    </div>
</div>

<div class="card mb-4">
    <div class="card-header"><i class="fas fa-user me-2"></i>Gebruiker</div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-4 mb-3">
                <label for="voornaam" class="form-label">Voornaam <span class="text-danger">*</span></label>
                <input type="text"
                       class="form-control @error('voornaam') is-invalid @enderror"
                       id="voornaam"
                       name="voornaam"
                       value="{{ old('voornaam', $medewerker->gebruiker->voornaam ?? '') }}"
                       required
                       maxlength="50"
                       data-medewerker-validate="voornaam">
                @error('voornaam')<div class="invalid-feedback">{{ $message }}</div>@enderror
                <div class="invalid-feedback d-none" data-client-error="voornaam"></div>
            </div>
            <div class="col-md-4 mb-3">
                <label for="tussenvoegsel" class="form-label">Tussenvoegsel</label>
                <input type="text"
                       class="form-control @error('tussenvoegsel') is-invalid @enderror"
                       id="tussenvoegsel"
                       name="tussenvoegsel"
                       value="{{ old('tussenvoegsel', $medewerker->gebruiker->tussenvoegsel ?? '') }}"
                       maxlength="20">
                @error('tussenvoegsel')<div class="invalid-feedback">{{ $message }}</div>@enderror
                <div class="invalid-feedback d-none" data-client-error="tussenvoegsel"></div>
            </div>
            <div class="col-md-4 mb-3">
                <label for="achternaam" class="form-label">Achternaam <span class="text-danger">*</span></label>
                <input type="text"
                       class="form-control @error('achternaam') is-invalid @enderror"
                       id="achternaam"
                       name="achternaam"
                       value="{{ old('achternaam', $medewerker->gebruiker->achternaam ?? '') }}"
                       required
                       maxlength="50"
                       data-medewerker-validate="achternaam">
                @error('achternaam')<div class="invalid-feedback">{{ $message }}</div>@enderror
                <div class="invalid-feedback d-none" data-client-error="achternaam"></div>
            </div>
        </div>
    </div>
</div>

<div class="card mb-4">
    <div class="card-header"><i class="fas fa-scissors me-2"></i>Medewerker</div>
    <div class="card-body">
        <div class="mb-3">
            <label for="specialisatie_id" class="form-label">Specialisatie <span class="text-danger">*</span></label>
            <select class="form-control @error('specialisatie_id') is-invalid @enderror"
                    id="specialisatie_id"
                    name="specialisatie_id"
                    required
                    data-medewerker-validate="specialisatie">
                <option value="">Selecteer specialisatie</option>
                @foreach($specialisaties as $specialisatie)
                    <option value="{{ $specialisatie->id }}"
                        {{ old('specialisatie_id', $medewerker->specialisatie_id ?? '') == $specialisatie->id ? 'selected' : '' }}>
                        {{ $specialisatie->naam }}
                    </option>
                @endforeach
            </select>
            @error('specialisatie_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="mb-0">
            <label for="is_actief" class="form-label">Status <span class="text-danger">*</span></label>
            @php $actief = old('is_actief', isset($medewerker) ? ($medewerker->is_actief ? '1' : '0') : '1'); @endphp
            <select class="form-control @error('is_actief') is-invalid @enderror"
                    id="is_actief"
                    name="is_actief"
                    required>
                <option value="1" {{ $actief == '1' ? 'selected' : '' }}>Actief</option>
                <option value="0" {{ $actief == '0' ? 'selected' : '' }}>Inactief</option>
            </select>
            @error('is_actief')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
    </div>
</div>

@include('medewerker.partials.beschikbaarheid')
