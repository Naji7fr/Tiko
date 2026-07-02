/**
 * Tiko — globale UI: delete-bevestiging & mobiele navigatie
 */
document.addEventListener('DOMContentLoaded', () => {
    initDeleteConfirmModal();
    initMobileNav();
});

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

            if (isActief) {
                if (blockedNameEl) {
                    blockedNameEl.textContent = name;
                }
                blockedModal?.show();
                return;
            }

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
