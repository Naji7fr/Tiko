/**
 * medewerker.validation.js — Client-side validatie (HTML5 aanvulling).
 *
 * Werkt samen met server-side FormRequest validatie.
 * PSR-12 / duidelijke functienamen in camelCase (JavaScript conventie).
 */
(function () {
    'use strict';

    const telefoonPattern = /^(?:\+31|0031|0)[1-9][0-9]{8}$/;
    const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

    /**
     * Toont een client-side foutmelding bij een veld.
     */
    function toonVeldFout(veldId, bericht) {
        const veld = document.getElementById(veldId);
        if (!veld) {
            return;
        }

        veld.classList.add('is-invalid');

        const feedback = document.querySelector('[data-client-error="' + veldId + '"]');
        if (feedback) {
            feedback.textContent = bericht;
            feedback.classList.remove('d-none');
        }
    }

    /**
     * Verwijdert client-side foutmarkering van een veld.
     */
    function wisVeldFout(veldId) {
        const veld = document.getElementById(veldId);
        if (!veld) {
            return;
        }

        veld.classList.remove('is-invalid');

        const feedback = document.querySelector('[data-client-error="' + veldId + '"]');
        if (feedback) {
            feedback.textContent = '';
            feedback.classList.add('d-none');
        }
    }

    /**
     * Valideert het volledige medewerkerformulier.
     */
    function valideerMedewerkerFormulier(formulier) {
        let isGeldig = true;

        const voornaam = formulier.querySelector('#voornaam');
        const achternaam = formulier.querySelector('#achternaam');
        const email = formulier.querySelector('#email');
        const telefoon = formulier.querySelector('#telefoon');
        const specialisatie = formulier.querySelector('#specialisatie_id');

        wisVeldFout('voornaam');
        wisVeldFout('achternaam');
        wisVeldFout('email');
        wisVeldFout('telefoon');

        if (!voornaam || voornaam.value.trim().length === 0) {
            toonVeldFout('voornaam', 'Voornaam is verplicht.');
            isGeldig = false;
        }

        if (!achternaam || achternaam.value.trim().length === 0) {
            toonVeldFout('achternaam', 'Achternaam is verplicht.');
            isGeldig = false;
        }

        if (!email || !emailPattern.test(email.value.trim())) {
            toonVeldFout('email', 'Voer een geldig e-mailadres in.');
            isGeldig = false;
        }

        if (telefoon && telefoon.value.trim() !== '' && !telefoonPattern.test(telefoon.value.trim())) {
            toonVeldFout('telefoon', 'Ongeldig telefoonnummer.');
            isGeldig = false;
        }

        if (!specialisatie || specialisatie.value === '') {
            specialisatie.classList.add('is-invalid');
            isGeldig = false;
        }

        return isGeldig;
    }

    document.addEventListener('DOMContentLoaded', function () {
        const formulier = document.getElementById('medewerker-form');
        if (!formulier) {
            return;
        }

        formulier.addEventListener('submit', function (event) {
            if (!valideerMedewerkerFormulier(formulier)) {
                event.preventDefault();
                event.stopPropagation();
            }
        });
    });
})();
