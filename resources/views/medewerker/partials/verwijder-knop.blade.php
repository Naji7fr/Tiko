{{-- Verwijder-knop medewerker (desktop + mobiel) --}}
<button
    type="button"
    class="btn {{ $class ?? 'btn-sm btn-danger' }}"
    title="Verwijderen"
    data-delete-trigger
    data-delete-url="{{ route('medewerkers.destroy', $medewerkerId) }}"
    data-delete-name="{{ $medewerker->naam }}"
    @if($isActief) data-delete-actief="1" @endif
>
    @if($showLabel ?? false)
        <i class="fas fa-trash me-1"></i>Verwijderen
    @else
        <i class="fas fa-trash"></i>
    @endif
</button>
