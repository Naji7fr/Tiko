/**
 * register.validation.js — Client-side validatie voor klant-registratie.
 */
(function () {
    'use strict';

    document.addEventListener('DOMContentLoaded', function () {
        const formulier = document.getElementById('register-form');
        const validatie = window.TikoFormValidatie;

        if (!formulier || !validatie) {
            return;
        }

        formulier.addEventListener('submit', function (event) {
            let isGeldig = true;

            const voornaam = formulier.querySelector('#voornaam');
            const achternaam = formulier.querySelector('#achternaam');
            const email = formulier.querySelector('#email');

            if (!validatie.valideerNaam(voornaam, 'Voornaam')) {
                isGeldig = false;
            }

            if (!validatie.valideerNaam(achternaam, 'Achternaam')) {
                isGeldig = false;
            }

            if (!validatie.valideerEmail(email)) {
                isGeldig = false;
            }

            if (!isGeldig) {
                event.preventDefault();
                event.stopPropagation();
            }
        });
    });
})();
