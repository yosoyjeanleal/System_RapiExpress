        // ===============================
            // VALIDACIONES DINÁMICAS
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

            $('input[name="Nombre_Instrumento"]').on('input', function() {
                const regex = /^[a-zA-Z0-9\sáéíóúÁÉÍÓÚñÑ.,\-()_]+$/;
                mostrarEstado($(this), regex.test($(this).val().trim()) ? 'ok' : 'error', 'Solo letras, números y caracteres válidos (,.-())');
            });

            $('input[name="Paquete_Peso"]').on('input', function() {
                const valor = parseFloat($(this).val());
                mostrarEstado($(this), valor > 0 ? 'ok' : 'error', 'El peso debe ser mayor a 0');
            });

            $('select[name="ID_Cliente"]').on('change', function() {
                mostrarEstado($(this), $(this).val() ? 'ok' : 'error', 'Debe seleccionar un cliente');
            });

            $('select[name="ID_Categoria"]').on('change', function() {
                mostrarEstado($(this), $(this).val() ? 'ok' : 'error', 'Debe seleccionar una categoría');
            });

            $('select[name="ID_Courier"]').on('change', function() {
                mostrarEstado($(this), $(this).val() ? 'ok' : 'error', 'Debe seleccionar un courier');
            });

            $('select[name="ID_Sucursal"]').on('change', function() {
                mostrarEstado($(this), $(this).val() ? 'ok' : 'error', 'Debe seleccionar una sucursal');
            });
