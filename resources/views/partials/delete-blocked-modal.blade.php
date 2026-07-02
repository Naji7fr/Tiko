{{-- Modal: actieve medewerker kan niet worden verwijderd --}}
<div class="modal fade" id="deleteBlockedModal" tabindex="-1" aria-labelledby="deleteBlockedModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title" id="deleteBlockedModalLabel">
                    <i class="fas fa-ban text-warning me-2"></i>Verwijderen niet mogelijk
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Sluiten"></button>
            </div>
            <div class="modal-body">
                <p class="mb-0">
                    Je kunt <strong id="deleteBlockedName">deze medewerker</strong> niet verwijderen omdat de status <strong>Actief</strong> is.
                </p>
            </div>
            <div class="modal-footer border-0 pt-0">
                <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Oké</button>
            </div>
        </div>
    </div>
</div>
