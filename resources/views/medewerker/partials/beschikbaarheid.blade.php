{{-- medewerker.partial — Weekrooster (beschikbaarheid per dag) --}}
@php
    use App\Services\Medewerker\MedewerkerBeschikbaarheidService;
    $dagen = MedewerkerBeschikbaarheidService::DAG_NAMEN;
@endphp

<div class="card mb-4">
    <div class="card-header"><i class="fas fa-calendar-week me-2"></i>Beschikbaarheid</div>
    <div class="card-body">
        <p class="text-muted small mb-3">
            Stel per dag in wanneer de medewerker beschikbaar is voor afspraken.
        </p>

        <div class="table-responsive">
            <table class="table table-sm align-middle mb-0">
                <thead>
                    <tr>
                        <th style="width: 4rem;">Werkt</th>
                        <th>Dag</th>
                        <th style="width: 8rem;">Start</th>
                        <th style="width: 8rem;">Eind</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($dagen as $dagNummer => $dagNaam)
                        @php
                            $dag = $beschikbaarheidPerDag[$dagNummer] ?? ['actief' => false, 'start' => '09:00', 'eind' => '18:00'];
                            $isActief = $dag['actief'] ?? false;
                        @endphp
                        <tr>
                            <td>
                                <input type="checkbox"
                                       class="form-check-input beschikbaarheid-actief"
                                       id="beschikbaarheid_{{ $dagNummer }}_actief"
                                       name="beschikbaarheid[{{ $dagNummer }}][actief]"
                                       value="1"
                                       {{ $isActief ? 'checked' : '' }}>
                            </td>
                            <td>
                                <label for="beschikbaarheid_{{ $dagNummer }}_actief" class="mb-0">{{ $dagNaam }}</label>
                            </td>
                            <td>
                                <input type="time"
                                       class="form-control form-control-sm beschikbaarheid-tijd @error("beschikbaarheid.{$dagNummer}.start") is-invalid @enderror"
                                       name="beschikbaarheid[{{ $dagNummer }}][start]"
                                       value="{{ $dag['start'] ?? '09:00' }}"
                                       {{ $isActief ? '' : 'disabled' }}>
                                @error("beschikbaarheid.{$dagNummer}.start")
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </td>
                            <td>
                                <input type="time"
                                       class="form-control form-control-sm beschikbaarheid-tijd @error("beschikbaarheid.{$dagNummer}.eind") is-invalid @enderror"
                                       name="beschikbaarheid[{{ $dagNummer }}][eind]"
                                       value="{{ $dag['eind'] ?? '18:00' }}"
                                       {{ $isActief ? '' : 'disabled' }}>
                                @error("beschikbaarheid.{$dagNummer}.eind")
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.beschikbaarheid-actief').forEach(function (checkbox) {
        var row = checkbox.closest('tr');
        var tijdVelden = row.querySelectorAll('.beschikbaarheid-tijd');

        function syncVelden() {
            tijdVelden.forEach(function (veld) {
                veld.disabled = !checkbox.checked;
            });
        }

        checkbox.addEventListener('change', syncVelden);
        syncVelden();
    });

    var form = document.getElementById('medewerker-form');
    if (form) {
        form.addEventListener('submit', function () {
            document.querySelectorAll('.beschikbaarheid-tijd').forEach(function (veld) {
                veld.disabled = false;
            });
        });
    }
});
</script>
@endpush
