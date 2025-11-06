
    // ===============================
    // FUNCIONES AUXILIARES
    // ===============================
    function mostrarEstado($input, estado, mensaje = '') {
        if (estado === 'ok') {
            $input.removeClass('is-invalid').addClass('is-valid');
            $input.next('.invalid-feedback').remove();
        } else {
            $input.removeClass('is-valid').addClass('is-invalid');
            if ($input.next('.invalid-feedback').length === 0) {
                $input.after(`<div class="invalid-feedback">${mensaje}</div>`);
            } else {
                $input.next('.invalid-feedback').text(mensaje);
            }
        }
    }

    // ===============================
    // VALIDACIONES DINÁMICAS
    // ===============================
    $('input[name="RIF_Sucursal"]').on('input', function() {
        const regex = /^[JGVEP]-\d{8}-\d$/;
        mostrarEstado($(this), regex.test($(this).val().trim()) ? 'ok' : 'error', 'Formato RIF inválido (Ej: J-12345678-9)');
    });

    $('input[name="Sucursal_Nombre"]').on('input', function() {
        const regex = /^[a-zA-Z0-9\sáéíóúÁÉÍÓÚñÑ.,\-()_]+$/;
        mostrarEstado($(this), regex.test($(this).val().trim()) ? 'ok' : 'error', 'Solo letras, números y caracteres válidos (,.-())');
    });

    $('input[name="Sucursal_Telefono"]').on('input', function() {
        const regex = /^\d{7,20}$/;
        mostrarEstado($(this), regex.test($(this).val().trim()) ? 'ok' : 'error', 'Teléfono inválido');
    });

    $('input[name="Sucursal_Correo"]').on('input', function() {
        const regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        mostrarEstado($(this), regex.test($(this).val().trim()) ? 'ok' : 'error', 'Correo inválido');
    });
