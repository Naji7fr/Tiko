{{-- Bootstrap modal: bevestiging vóór verwijderen --}}
<div class="modal fade" id="deleteConfirmModal" tabindex="-1" aria-labelledby="deleteConfirmModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title" id="deleteConfirmModalLabel">
                    <i class="fas fa-exclamation-triangle text-danger me-2"></i>Verwijderen bevestigen
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Sluiten"></button>
            </div>
            <div class="modal-body">
                <p class="mb-0">
                    Weet je zeker dat je <strong id="deleteConfirmName">dit item</strong> wilt verwijderen?
                </p>
                <p class="text-muted small mb-0 mt-2">Deze actie kan niet ongedaan worden gemaakt.</p>
            </div>
            <div class="modal-footer border-0 pt-0">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuleren</button>
                <form id="deleteConfirmForm" method="POST" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-trash me-1"></i>Ja, verwijderen
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
