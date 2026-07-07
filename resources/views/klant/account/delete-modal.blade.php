<div class="modal fade" id="deleteAccountModal" tabindex="-1" aria-labelledby="deleteAccountModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title" id="deleteAccountModalLabel">
                    <i class="fas fa-triangle-exclamation text-danger me-2"></i>Account verwijderen
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Sluiten"></button>
            </div>
            <div class="modal-body">
                <p class="mb-0">
                    Weet je zeker dat je je account wilt verwijderen? Je gegevens, login en gekoppelde klantinformatie worden permanent verwijderd.
                </p>
                <p class="text-muted small mb-0 mt-2">Deze actie kan niet ongedaan worden gemaakt.</p>
            </div>
            <div class="modal-footer border-0 pt-0">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuleren</button>
                <form method="POST" action="{{ route('klant.overzicht.destroy') }}" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-user-xmark me-1"></i>Ja, verwijder mijn account
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>