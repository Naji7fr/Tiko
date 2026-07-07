/**
 * afspraak-planning.js — Dynamische keuze: behandeling → specialist → datum → starttijd.
 */
(function () {
    'use strict';

    const container = document.getElementById('afspraak-planning');

    if (!container) {
        return;
    }

    const behandelingSelect = document.getElementById('behandeling_id');
    const medewerkerSelect = document.getElementById('medewerker_id');
    const datumSelect = document.getElementById('afspraak_datum');
    const tijdSelect = document.getElementById('afspraak_tijd');
    const meldingBox = document.getElementById('afspraak-planning-melding');
    const meldingTekst = document.getElementById('afspraak-planning-melding-tekst');
    const submitButton = container.closest('form')?.querySelector('button[type="submit"]');

    const urls = {
        medewerkers: container.dataset.medewerkersUrl,
        datums: container.dataset.datumsUrl,
        tijden: container.dataset.tijdenUrl,
    };

    const initial = {
        behandeling: container.dataset.behavioraling || '',
        medewerker: container.dataset.medewerker || '',
        datum: container.dataset.datum || '',
        tijd: container.dataset.tijd || '',
        afspraakId: container.dataset.afspraakId || '',
    };

    function resetSelect(select, placeholder, disabled) {
        select.innerHTML = '';
        const option = document.createElement('option');
        option.value = '';
        option.textContent = placeholder;
        select.appendChild(option);
        select.disabled = disabled;
        select.value = '';
    }

    function vulSelect(select, items, valueKey, labelKey, selectedValue, placeholder) {
        resetSelect(select, placeholder, false);

        items.forEach(function (item) {
            const option = document.createElement('option');
            option.value = item[valueKey];
            option.textContent = item[labelKey];
            if (String(item[valueKey]) === String(selectedValue)) {
                option.selected = true;
            }
            select.appendChild(option);
        });

        if (items.length === 0) {
            select.disabled = true;
        }
    }

    function toonMelding(tekst) {
        if (!tekst) {
            meldingBox.classList.add('d-none');
            meldingTekst.textContent = '';
            if (submitButton) {
                submitButton.disabled = false;
            }
            return;
        }

        meldingTekst.textContent = tekst;
        meldingBox.classList.remove('d-none');
        if (submitButton) {
            submitButton.disabled = true;
        }
    }

    function queryParams(extra) {
        const params = new URLSearchParams(extra);

        if (initial.afspraakId) {
            params.set('afspraak_id', initial.afspraakId);
        }

        return params.toString();
    }

    async function fetchJson(url) {
        const response = await fetch(url, {
            headers: {
                Accept: 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
            },
            credentials: 'same-origin',
        });

        if (!response.ok) {
            throw new Error('Kon beschikbaarheid niet ophalen.');
        }

        return response.json();
    }

    async function laadMedewerkers(behandelingId, selectedMedewerker) {
        resetSelect(medewerkerSelect, 'Laden...', true);
        resetSelect(datumSelect, 'Kies eerst een specialist', true);
        resetSelect(tijdSelect, 'Kies eerst een datum', true);
        toonMelding('');

        if (!behandelingId) {
            resetSelect(medewerkerSelect, 'Kies eerst een behandeling', true);
            return;
        }

        const data = await fetchJson(urls.medewerkers + '?' + queryParams({ behandeling_id: behandelingId }));
        const medewerkers = (data.medewerkers || []).map(function (medewerker) {
            const label = medewerker.specialisatie
                ? medewerker.naam + ' (' + medewerker.specialisatie + ')'
                : medewerker.naam;

            return {
                id: medewerker.id,
                label: label,
            };
        });

        vulSelect(medewerkerSelect, medewerkers, 'id', 'label', selectedMedewerker, 'Kies een specialist');

        if (medewerkers.length === 0) {
            toonMelding('Geen beschikbare tijden gevonden');
        }
    }

    async function laadDatums(behandelingId, medewerkerId, selectedDatum) {
        resetSelect(datumSelect, 'Laden...', true);
        resetSelect(tijdSelect, 'Kies eerst een datum', true);
        toonMelding('');

        if (!behandelingId || !medewerkerId) {
            resetSelect(datumSelect, 'Kies eerst een specialist', true);
            return;
        }

        const data = await fetchJson(urls.datums + '?' + queryParams({
            behandeling_id: behandelingId,
            medewerker_id: medewerkerId,
        }));

        const datums = (data.datums || []).map(function (datum) {
            const parsed = new Date(datum + 'T00:00:00');
            const label = parsed.toLocaleDateString('nl-NL', {
                weekday: 'short',
                day: '2-digit',
                month: '2-digit',
                year: 'numeric',
            });

            return { value: datum, label: label };
        });

        vulSelect(datumSelect, datums, 'value', 'label', selectedDatum, 'Kies een datum');

        if (datums.length === 0) {
            toonMelding(data.melding || 'Geen beschikbare tijden gevonden');
        }
    }

    async function laadTijden(behandelingId, medewerkerId, datum, selectedTijd) {
        resetSelect(tijdSelect, 'Laden...', true);
        toonMelding('');

        if (!behandelingId || !medewerkerId || !datum) {
            resetSelect(tijdSelect, 'Kies eerst een datum', true);
            return;
        }

        const data = await fetchJson(urls.tijden + '?' + queryParams({
            behandeling_id: behandelingId,
            medewerker_id: medewerkerId,
            datum: datum,
        }));

        const tijden = (data.tijden || []).map(function (tijd) {
            return { value: tijd, label: tijd };
        });

        vulSelect(tijdSelect, tijden, 'value', 'label', selectedTijd, 'Kies een starttijd');

        if (tijden.length === 0) {
            toonMelding(data.melding || 'Geen beschikbare tijden gevonden');
        }
    }

    behandelingSelect.addEventListener('change', async function () {
        const behandelingId = behandelingSelect.value;
        await laadMedewerkers(behandelingId, '');
    });

    medewerkerSelect.addEventListener('change', async function () {
        await laadDatums(behandelingSelect.value, medewerkerSelect.value, '');
    });

    datumSelect.addEventListener('change', async function () {
        await laadTijden(behandelingSelect.value, medewerkerSelect.value, datumSelect.value, '');
    });

    tijdSelect.addEventListener('change', function () {
        if (tijdSelect.value) {
            toonMelding('');
        }
    });

    async function initialiseer() {
        if (!initial.behandeling) {
            resetSelect(medewerkerSelect, 'Kies eerst een behandeling', true);
            resetSelect(datumSelect, 'Kies eerst een specialist', true);
            resetSelect(tijdSelect, 'Kies eerst een datum', true);
            return;
        }

        await laadMedewerkers(initial.behandeling, initial.medewerker);

        if (initial.medewerker) {
            await laadDatums(initial.behandeling, initial.medewerker, initial.datum);
        }

        if (initial.datum) {
            await laadTijden(initial.behandeling, initial.medewerker, initial.datum, initial.tijd);
        }
    }

    initialiseer().catch(function () {
        toonMelding('Kon beschikbare momenten niet laden. Probeer de pagina te vernieuwen.');
    });
})();
