// SCRIPT PARA MODAL (abrir/cerrar) – SIN tocar el envío del formulario
document.addEventListener('DOMContentLoaded', () => {
    const backdrop   = document.getElementById('modalBackdrop');
    const fabAdd     = document.getElementById('fabAdd');
    const btnClose   = document.getElementById('btnClose');
    const btnCancel  = document.getElementById('btnCancel');
    const form       = document.getElementById('genreForm');
    const temaInput  = document.getElementById('tema');
    const sourcesSection = document.getElementById('sourcesSection');
    const sourcesList    = document.getElementById('sourcesList');

    if (!backdrop || !fabAdd || !btnClose || !btnCancel || !form) {
        console.warn('No se encontraron elementos del modal. Revisa los IDs.');
        return;
    }

    function openModal() {
        // Si usas la clase "open" en CSS para mostrar el modal, mantenemos eso
        backdrop.classList.add('open');
        backdrop.setAttribute('aria-hidden', 'false');

        // Opcional: también puedes usar display flex si quieres:
        // backdrop.style.display = 'flex';

        // Foco en el primer campo
        if (temaInput) {
            setTimeout(() => temaInput.focus(), 0);
        }

        document.addEventListener('keydown', onEsc);
    }

    function closeModal() {
        backdrop.classList.remove('open');
        backdrop.setAttribute('aria-hidden', 'true');

        // Opcional: si usas display:
        // backdrop.style.display = 'none';

        document.removeEventListener('keydown', onEsc);

        // Limpiar formulario y fuentes
        form.reset();
        if (sourcesList) {
            sourcesList.innerHTML = '';
        }
        if (sourcesSection) {
            sourcesSection.style.display = 'none';
        }
    }

    function onEsc(e) {
        if (e.key === 'Escape') {
            closeModal();
        }
    }

    // Abrir modal
    fabAdd.addEventListener('click', openModal);

    // Cerrar con la X
    btnClose.addEventListener('click', (e) => {
        e.preventDefault();
        closeModal();
    });

    // Cerrar con Cancelar
    btnCancel.addEventListener('click', (e) => {
        e.preventDefault();
        closeModal();
    });

    // Cerrar haciendo click fuera del cuadro del modal
    backdrop.addEventListener('click', (e) => {
        if (e.target === backdrop) {
            closeModal();
        }
    });
});
    //  <!-- HOLA RUBEN -->
