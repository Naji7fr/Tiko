@php
    $medewerkerId = $medewerker->id ?? $medewerker->medewerker_id;
    $statusLabel = $medewerker->status ?? ($medewerker->is_actief ? 'Actief' : 'Inactief');
    $isActief = $statusLabel === 'Actief';
@endphp
<div class="medewerker-mobile-card card mb-3">
    <div class="card-body">
        <div class="d-flex align-items-center mb-3">
            <div class="avatar-circle me-3">
                {{ strtoupper(substr($medewerker->naam ?? '', 0, 1)) }}
            </div>
            <div>
                <h3 class="h6 mb-1">{{ $medewerker->naam }}</h3>
                @if($isActief)
                    <span class="badge bg-success"><i class="fas fa-check-circle me-1"></i>Actief</span>
                @else
                    <span class="badge bg-secondary"><i class="fas fa-pause-circle me-1"></i>{{ $statusLabel }}</span>
                @endif
            </div>
        </div>

        <ul class="medewerker-mobile-card__meta list-unstyled mb-3">
            <li>
                <i class="fas fa-envelope text-muted me-2"></i>
                <a href="mailto:{{ $medewerker->email }}" class="text-decoration-none">{{ $medewerker->email }}</a>
            </li>
            <li>
                <i class="fas fa-phone text-muted me-2"></i>
                {{ $medewerker->telefoonnummer ?? $medewerker->telefoon ?? '—' }}
            </li>
            <li>
                <i class="fas fa-scissors text-muted me-2"></i>
                {{ $medewerker->specialisatie?->naam ?? $medewerker->specialisatie_naam ?? '—' }}
            </li>
        </ul>

        <div class="d-grid gap-2 d-sm-flex">
            <a href="{{ route('medewerkers.edit', $medewerkerId) }}" class="btn btn-warning flex-fill">
                <i class="fas fa-edit me-1"></i>Wijzigen
            </a>
            @include('medewerker.partials.verwijder-knop', [
                'medewerker' => $medewerker,
                'medewerkerId' => $medewerkerId,
                'isActief' => $isActief,
                'class' => 'btn btn-danger flex-fill',
                'showLabel' => true,
            ])
        </div>
    </div>
</div>
