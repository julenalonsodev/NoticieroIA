document.addEventListener('DOMContentLoaded', function () {
    const addSourcesSelect = document.getElementById('addSources');
    const sourcesSection   = document.getElementById('sourcesSection');
    const btnAddSource     = document.getElementById('btnAddSource');
    const sourcesList      = document.getElementById('sourcesList');
    const genreForm        = document.getElementById('genreForm');
    const hiddenFuentes    = document.getElementById('fuentes');

    if (!addSourcesSelect || !sourcesSection || !btnAddSource || !sourcesList || !genreForm || !hiddenFuentes) {
        console.warn('No se encontraron elementos de FUENTES. Revisa los IDs.');
        return;
    }

    function toggleSourcesSection() {
        if (addSourcesSelect.value === 'si') {
            sourcesSection.style.display = 'block';
        } else {
            sourcesSection.style.display = 'none';
            sourcesList.innerHTML = '';
        }
    }

    // Estado inicial
    toggleSourcesSection();

    addSourcesSelect.addEventListener('change', toggleSourcesSection);

    // Añadir campo de fuente
    btnAddSource.addEventListener('click', () => {
        const row = document.createElement('div');
        row.className = 'source-row';

        const input = document.createElement('input');
        input.type = 'text';
        input.name = 'sourceItem';
        input.placeholder = 'Ej: https://example.com';
        input.className = 'source-input';

        const removeBtn = document.createElement('button');
        removeBtn.type = 'button';
        removeBtn.className = 'btn ghost remove-source';
        removeBtn.textContent = 'Eliminar';
        removeBtn.addEventListener('click', () => row.remove());

        row.appendChild(input);
        row.appendChild(removeBtn);
        sourcesList.appendChild(row);
    });

    // Antes de enviar el formulario, empaquetamos las fuentes en JSON en el hidden
    genreForm.addEventListener('submit', function () {
        const sourcesInputs = sourcesList.querySelectorAll('input[name="sourceItem"]');
        const fuentesArray = [];

        sourcesInputs.forEach(input => {
            const value = input.value.trim();
            if (value !== '') {
                fuentesArray.push(value);
            }
        });

        if (addSourcesSelect.value === 'si' && fuentesArray.length > 0) {
            hiddenFuentes.value = JSON.stringify(fuentesArray);
        } else {
            hiddenFuentes.value = '';
        }
        // OJO: no usamos preventDefault → el formulario se envía al PHP
    });
});
