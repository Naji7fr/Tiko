{{-- account.view — Gedeeld formulier voor accounts --}}
<div class="card mb-4">
    <div class="card-header"><i class="fas fa-user me-2"></i>Accountgegevens</div>
    <div class="card-body">
        <div class="row g-3">
            <div class="col-md-6">
                <label for="voornaam" class="form-label">Voornaam <span class="text-danger">*</span></label>
                <input type="text"
                       class="form-control @error('voornaam') is-invalid @enderror"
                       id="voornaam"
                       name="voornaam"
                       value="{{ old('voornaam', $account->voornaam ?? '') }}"
                       required
                      maxlength="50"
                      pattern="[A-Za-zÀ-ÖØ-öø-ÿ\s\-\.'']+">
                @error('voornaam')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-6">
                <label for="achternaam" class="form-label">Achternaam <span class="text-danger">*</span></label>
                <input type="text"
                       class="form-control @error('achternaam') is-invalid @enderror"
                       id="achternaam"
                       name="achternaam"
                       value="{{ old('achternaam', $account->achternaam ?? '') }}"
                       required
                      maxlength="50"
                      pattern="[A-Za-zÀ-ÖØ-öø-ÿ\s\-\.'']+">
                @error('achternaam')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-6">
                <label for="email" class="form-label">E-mailadres <span class="text-danger">*</span></label>
                <input type="email"
                       class="form-control @error('email') is-invalid @enderror"
                       id="email"
                       name="email"
                       value="{{ old('email', $account->email ?? '') }}"
                       required
                       maxlength="255">
                @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-6">
                <label for="telefoon" class="form-label">Telefoon <span class="text-muted">(optioneel, klant)</span></label>
                <input type="text"
                       class="form-control @error('telefoon') is-invalid @enderror"
                       id="telefoon"
                       name="telefoon"
                       value="{{ old('telefoon', $telefoon ?? '') }}"
                      maxlength="25"
                      pattern="^(?:\+31|0031|0)[1-9][0-9]{8}$"
                       placeholder="0612345678">
                @error('telefoon')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-6">
                <label for="role" class="form-label">Rol <span class="text-danger">*</span></label>
                <select class="form-select @error('role') is-invalid @enderror"
                        id="role"
                        name="role"
                        required
                        @if(isset($account) && $account->id === auth()->id()) disabled @endif>
                    <option value="admin" @selected(old('role', $account->role ?? '') === 'admin')>Eigenaar</option>
                    <option value="medewerker" @selected(old('role', $account->role ?? '') === 'medewerker')>Medewerker</option>
                    <option value="klant" @selected(old('role', $account->role ?? '') === 'klant')>Klant</option>
                </select>
                @if(isset($account) && $account->id === auth()->id())
                    <input type="hidden" name="role" value="admin">
                @endif
                @error('role')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-6">
                <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
                <select class="form-select @error('status') is-invalid @enderror"
                        id="status"
                        name="status"
                        required
                        @if(isset($account) && $account->id === auth()->id()) disabled @endif>
                    <option value="Actief" @selected(old('status', $account->status ?? 'Actief') === 'Actief')>Actief</option>
                    <option value="Inactief" @selected(old('status', $account->status ?? '') === 'Inactief')>Inactief</option>
                </select>
                @if(isset($account) && $account->id === auth()->id())
                    <input type="hidden" name="status" value="Actief">
                @endif
                @error('status')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
        </div>
    </div>
</div>

<div class="card mb-4">
    <div class="card-header"><i class="fas fa-lock me-2"></i>Wachtwoord</div>
    <div class="card-body">
        <div class="row g-3">
            <div class="col-md-6">
                <label for="password" class="form-label">
                    Wachtwoord @if(!isset($account))<span class="text-danger">*</span>@else<span class="text-muted">(leeg = ongewijzigd)</span>@endif
                </label>
                <input type="password"
                       class="form-control @error('password') is-invalid @enderror"
                       id="password"
                       name="password"
                       @if(!isset($account)) required @endif
                       minlength="8"
                       autocomplete="new-password">
                @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-6">
                <label for="password_confirmation" class="form-label">Bevestig wachtwoord</label>
                <input type="password"
                       class="form-control"
                       id="password_confirmation"
                       name="password_confirmation"
                       minlength="8"
                       autocomplete="new-password">
            </div>
        </div>
    </div>
</div>
