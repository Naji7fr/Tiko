@php
    $medewerkerId = $medewerker->id ?? $medewerker->medewerker_id;
    $statusLabel = $medewerker->status ?? ($medewerker->is_actief ? 'Actief' : 'Inactief');
    $isActief = $statusLabel === 'Actief';
@endphp
<tr>
    <td>
        <div class="d-flex align-items-center">
            <div class="avatar-circle me-3">
                {{ strtoupper(substr($medewerker->naam ?? '', 0, 1)) }}
            </div>
            <strong>{{ $medewerker->naam }}</strong>
        </div>
    </td>
    <td>
        <a href="mailto:{{ $medewerker->email }}" class="text-decoration-none">
            <i class="fas fa-envelope me-2 text-muted"></i>{{ $medewerker->email }}
        </a>
    </td>
    <td>
        @if($medewerker->telefoonnummer ?? $medewerker->telefoon ?? null)
            <i class="fas fa-phone me-2 text-muted"></i>{{ $medewerker->telefoonnummer ?? $medewerker->telefoon }}
        @else
            <span class="text-muted">—</span>
        @endif
    </td>
    <td>
        @if($isActief)
            <span class="badge bg-success">
                <i class="fas fa-check-circle me-1"></i>Actief
            </span>
        @else
            <span class="badge bg-secondary">
                <i class="fas fa-pause-circle me-1"></i>{{ $statusLabel }}
            </span>
        @endif
    </td>
    <td>
        <span class="badge bg-primary">
            <i class="fas fa-scissors me-1"></i>{{ $medewerker->specialisatie?->naam ?? $medewerker->specialisatie_naam ?? '—' }}
        </span>
    </td>
    <td class="text-end">
        <div class="btn-group" role="group">
            <a href="{{ route('medewerkers.edit', $medewerkerId) }}" class="btn btn-sm btn-warning" title="Medewerker wijzigen">
                <i class="fas fa-edit me-1"></i>Wijzigen
            </a>
            @include('medewerker.partials.verwijder-knop', [
                'medewerker' => $medewerker,
                'medewerkerId' => $medewerkerId,
                'isActief' => $isActief,
            ])
        </div>
    </td>
</tr>
