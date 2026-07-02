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
    const confirmModal = confirmModalEl ? new bootstrap.Modal(confirmModalEl) : null;
    const blockedModal = blockedModalEl ? new bootstrap.Modal(blockedModalEl) : null;

    document.querySelectorAll('[data-delete-trigger]').forEach((trigger) => {
        trigger.addEventListener('click', (event) => {
            event.preventDefault();

            const name = trigger.dataset.deleteName || 'deze medewerker';
            const isActief = trigger.dataset.deleteActief === '1';

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
            if (nameEl) {
                nameEl.textContent = name;
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
