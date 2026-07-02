{{-- Modal: server-side validatiefouten (FormRequest) --}}
@if($errors->any())
    <div class="modal fade" id="validationErrorModal" tabindex="-1" aria-labelledby="validationErrorModalLabel" aria-hidden="true" data-auto-show="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title" id="validationErrorModalLabel">
                        <i class="fas fa-exclamation-circle text-danger me-2"></i>Controleer het formulier
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Sluiten"></button>
                </div>
                <div class="modal-body">
                    <ul class="mb-0 ps-3">
                        @foreach($errors->all() as $foutmelding)
                            <li>{{ $foutmelding }}</li>
                        @endforeach
                    </ul>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Oké</button>
                </div>
            </div>
        </div>
    </div>
@endif
