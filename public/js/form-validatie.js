/**
 * form-validatie.js — Client-side validatie voor naam (geen cijfers) en e-mail.
 */
(function () {
    'use strict';

    const naamPattern = /^[\p{L}\s\-'\.]+$/u;
    const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

    function toonVeldFout(veld, bericht) {
        if (!veld) {
            return;
        }

        veld.classList.add('is-invalid');

        const feedback = document.querySelector('[data-client-error="' + veld.id + '"]');
        if (feedback) {
            feedback.textContent = bericht;
            feedback.classList.remove('d-none');
        }
    }

    function wisVeldFout(veld) {
        if (!veld) {
            return;
        }

        veld.classList.remove('is-invalid');

        const feedback = document.querySelector('[data-client-error="' + veld.id + '"]');
        if (feedback) {
            feedback.textContent = '';
            feedback.classList.add('d-none');
        }
    }

    function valideerNaam(veld, label) {
        wisVeldFout(veld);

        const waarde = veld?.value.trim() ?? '';

        if (waarde.length === 0) {
            toonVeldFout(veld, label + ' is verplicht.');
            return false;
        }

        if (!naamPattern.test(waarde)) {
            toonVeldFout(veld, label + ' mag geen cijfers of speciale tekens bevatten.');
            return false;
        }

        return true;
    }

    function valideerOptioneleNaam(veld, label) {
        wisVeldFout(veld);

        const waarde = veld?.value.trim() ?? '';

        if (waarde.length === 0) {
            return true;
        }

        if (!naamPattern.test(waarde)) {
            toonVeldFout(veld, label + ' mag geen cijfers of speciale tekens bevatten.');
            return false;
        }

        return true;
    }

    function valideerEmail(veld) {
        wisVeldFout(veld);

        const waarde = veld?.value.trim() ?? '';

        if (waarde.length === 0) {
            toonVeldFout(veld, 'E-mailadres is verplicht.');
            return false;
        }

        if (!emailPattern.test(waarde)) {
            toonVeldFout(veld, 'Voer een geldig e-mailadres in.');
            return false;
        }

        return true;
    }

    window.TikoFormValidatie = {
        naamPattern: naamPattern,
        emailPattern: emailPattern,
        valideerNaam: valideerNaam,
        valideerOptioneleNaam: valideerOptioneleNaam,
        valideerEmail: valideerEmail,
        wisVeldFout: wisVeldFout,
    };
})();
