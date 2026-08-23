const form = document.getElementById('fizzbuzz-form');
const resultGrid = document.getElementById('result-grid');
const status = document.getElementById('status');
const resultCount = document.getElementById('result-count');
const statsBox = document.getElementById('stats-box');
const statsValues = document.getElementById('stats-values');
const combinedValue = document.getElementById('combined-value');

function updateCombinedValue() {
    combinedValue.textContent = `${form.elements.str1.value}${form.elements.str2.value}` || '...';
}

function showError(message) {
    status.textContent = message;
    status.className = 'status error';
}

function validateForm() {
    const requiredFields = [
        ['int1', 'le diviseur 1'],
        ['int2', 'le diviseur 2'],
        ['limit', 'la limite'],
        ['str1', 'le mot 1'],
        ['str2', 'le mot 2'],
    ];
    const missingFields = requiredFields
        .filter(([name]) => !form.elements[name].value.trim())
        .map(([, label]) => label);

    if (missingFields.length > 0) {
        showError(`Veuillez renseigner ${missingFields.join(', ')} avant de lancer la génération.`);
        resultCount.textContent = 'Champs requis';
        return false;
    }

    return true;
}

function renderResults(values) {
    const int1 = Number(form.elements.int1.value);
    const int2 = Number(form.elements.int2.value);

    resultGrid.replaceChildren(...values.map((item, index) => {
        const number = index + 1;
        const element = document.createElement('div');
        element.className = 'result-item';
        if (number % int1 === 0 && number % int2 === 0) {
            element.classList.add('is-combined');
        } else if (number % int1 === 0) {
            element.classList.add('is-first-divisor');
        } else if (number % int2 === 0) {
            element.classList.add('is-second-divisor');
        }
        element.textContent = typeof item === 'string' ? item : item.value;
        return element;
    }));
}

form.elements.str1.addEventListener('input', updateCombinedValue);
form.elements.str2.addEventListener('input', updateCombinedValue);
updateCombinedValue();

form.addEventListener('submit', async (event) => {
    event.preventDefault();
    if (!validateForm()) {
        return;
    }

    const params = new URLSearchParams(new FormData(form));
    status.textContent = 'Génération en cours...';
    status.className = 'status';
    resultCount.textContent = 'Chargement';
    statsBox.classList.remove('visible');

    try {
        const response = await fetch(`/api/fizzbuzz?${params.toString()}`, {
            headers: { Accept: 'application/json' },
        });
        const payload = await response.json();
        if (!response.ok) {
            throw new Error(payload.detail || "L'API a retourné une erreur.");
        }

        renderResults(payload);
        status.textContent = 'Séquence générée avec succès.';
        resultCount.textContent = `${payload.length} valeurs`;
    } catch (error) {
        resultGrid.replaceChildren();
        resultGrid.innerHTML = '<div class="empty-state">Aucune séquence générée.</div>';
        resultCount.textContent = 'Erreur';
        showError(error.message);
    }
});

document.getElementById('stats-button').addEventListener('click', async () => {
    status.textContent = 'Chargement des statistiques...';
    status.className = 'status';

    try {
        const response = await fetch('/api/statistics/most-frequent', {
            headers: { Accept: 'application/json' },
        });
        const payload = await response.json();
        if (!response.ok) {
            throw new Error(payload.detail || 'Impossible de charger les statistiques.');
        }

        if (!payload) {
            statsValues.textContent = "Aucune requête n'a encore été enregistrée.";
        } else {
            statsValues.replaceChildren(...[
                ['diviseur 1', payload.int1], ['diviseur 2', payload.int2],
                ['limite', payload.limit], ['mot 1', payload.str1],
                ['mot 2', payload.str2], ['utilisations', payload.hits],
            ].map(([label, value]) => {
                const element = document.createElement('span');
                element.textContent = `${label} : ${value}`;
                return element;
            }));
        }

        statsBox.classList.add('visible');
        status.textContent = 'Statistiques chargées.';
    } catch (error) {
        showError(error.message);
    }
});
