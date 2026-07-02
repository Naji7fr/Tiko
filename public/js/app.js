/**
 * app.js — Globale frontend-logica voor Tiko Barbershop
 *
 * - Delete-bevestiging modals (actief vs inactief medewerker)
 * - Validatiefouten popup (server-side FormRequest)
 * - Mobiele navigatie: menu sluiten na klik
 */
document.addEventListener('DOMContentLoaded', () => {
    initDeleteConfirmModal();
    initMobileNav();
    initValidationErrorModal();
    initAutoDismissFlashAlerts();
});

/**
 * Koppelt [data-delete-trigger] knoppen aan Bootstrap modals.
 * Actieve medewerkers → blocked modal; inactieve → confirm + form submit.
 */
function initDeleteConfirmModal() {
    const confirmModalEl = document.getElementById('deleteConfirmModal');
    const blockedModalEl = document.getElementById('deleteBlockedModal');

    if (!confirmModalEl && !blockedModalEl) {
        return;
    }

    const form = document.getElementById('deleteConfirmForm');
    const nameEl = document.getElementById('deleteConfirmName');
    const blockedNameEl = document.getElementById('deleteBlockedName');
    const titleEl = document.getElementById('deleteConfirmTitle');
    const hintEl = document.getElementById('deleteConfirmHint');
    const questionEl = document.getElementById('deleteConfirmQuestion');
    const submitLabelEl = document.getElementById('deleteConfirmSubmitLabel');
    const cancelBtn = document.getElementById('deleteConfirmCancel');
    const confirmModal = confirmModalEl ? new bootstrap.Modal(confirmModalEl) : null;
    const blockedModal = blockedModalEl ? new bootstrap.Modal(blockedModalEl) : null;

    document.querySelectorAll('[data-delete-trigger]').forEach((trigger) => {
        trigger.addEventListener('click', (event) => {
            event.preventDefault();

            const name = trigger.dataset.deleteName || 'deze medewerker';
            const isActief = trigger.dataset.deleteActief === '1';
            const mode = trigger.dataset.deleteMode || 'verwijderen';

            // Actieve medewerker: toon foutmodal, geen DELETE
            if (isActief) {
                if (blockedNameEl) {
                    blockedNameEl.textContent = name;
                }
                blockedModal?.show();
                return;
            }

            // Inactieve medewerker: bevestiging, daarna form POST met @method DELETE
            const url = trigger.dataset.deleteUrl;
            if (!url || !form) {
                return;
            }

            form.action = url;
            if (nameEl && mode !== 'annuleer' && mode !== 'verwijderen') {
                nameEl.textContent = name;
            }

            if (mode === 'annuleer') {
                if (titleEl) titleEl.textContent = 'Annulering bevestigen';
                if (questionEl) questionEl.innerHTML = `Weet je zeker dat je <strong>${name}</strong> wilt annuleren?`;
                if (hintEl) hintEl.textContent = 'Het tijdstip wordt direct weer vrijgegeven voor nieuwe afspraken.';
                if (submitLabelEl) submitLabelEl.textContent = 'Ja, annuleren';
                if (cancelBtn) cancelBtn.textContent = 'Terug';
            } else {
                if (titleEl) titleEl.textContent = 'Verwijderen bevestigen';
                if (questionEl) questionEl.innerHTML = `Weet je zeker dat je <strong>${name}</strong> wilt verwijderen?`;
                if (hintEl) hintEl.textContent = 'Deze actie kan niet ongedaan worden gemaakt.';
                if (submitLabelEl) submitLabelEl.textContent = 'Ja, verwijderen';
                if (cancelBtn) cancelBtn.textContent = 'Annuleren';
            }

            confirmModal?.show();
        });
    });
}

/** Toont validatiefouten automatisch in een Bootstrap modal. */
function initValidationErrorModal() {
    const modalEl = document.getElementById('validationErrorModal');

    if (!modalEl || modalEl.dataset.autoShow !== 'true') {
        return;
    }

    bootstrap.Modal.getOrCreateInstance(modalEl).show();
}

/** Verbergt success/info flash-meldingen automatisch na 5 seconden. */
function initAutoDismissFlashAlerts() {
    const dismissAfterMs = 5000;

    document.querySelectorAll('.alert-success, .alert-info').forEach((alertEl) => {
        if (!alertEl.classList.contains('alert-dismissible')) {
            return;
        }

        setTimeout(() => {
            bootstrap.Alert.getOrCreateInstance(alertEl).close();
        }, dismissAfterMs);
    });
}

/** Sluit het burger-menu automatisch na navigatie (viewport < 992px). */
function initMobileNav() {
    const toggler = document.querySelector('.navbar-toggler');
    const collapse = document.getElementById('navbarNav');

    if (!toggler || !collapse) {
        return;
    }

    collapse.querySelectorAll('.nav-link:not(.dropdown-toggle)').forEach((link) => {
        link.addEventListener('click', () => {
            if (window.innerWidth < 992 && collapse.classList.contains('show')) {
                bootstrap.Collapse.getOrCreateInstance(collapse).hide();
            }
        });
    });

    collapse.querySelectorAll('.dropdown-item').forEach((item) => {
        item.addEventListener('click', () => {
            if (window.innerWidth < 992 && collapse.classList.contains('show')) {
                bootstrap.Collapse.getOrCreateInstance(collapse).hide();
            }
        });
    });
}
