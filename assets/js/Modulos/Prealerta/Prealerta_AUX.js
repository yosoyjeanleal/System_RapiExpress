$(document).ready(function() {
    
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
    
    // Validación de Tracking Tienda
    $(document).on('input', 'input[name="Tracking_Tienda"]', function() {
        const regex = /^[A-Za-z0-9\-]{3,50}$/;
        const valor = $(this).val().trim();
        mostrarEstado(
            $(this), 
            regex.test(valor) ? 'ok' : 'error', 
            'El tracking solo puede contener letras, números o guiones (3 a 50 caracteres)'
        );
    });

    // Validación de Piezas
    $(document).on('input', 'input[name="Prealerta_Piezas"]', function() {
        const valor = parseInt($(this).val());
        mostrarEstado(
            $(this), 
            valor > 0 ? 'ok' : 'error', 
            'Debe ingresar un número válido de piezas mayor que 0'
        );
    });

    // Validación de Peso
    $(document).on('input', 'input[name="Prealerta_Peso"]', function() {
        const valor = parseFloat($(this).val());
        mostrarEstado(
            $(this), 
            valor > 0 ? 'ok' : 'error', 
            'Debe ingresar un peso válido mayor que 0'
        );
    });

    // Validación de Descripción
    $(document).on('input', 'textarea[name="Prealerta_Descripcion"]', function() {
        const valor = $(this).val().trim();
        mostrarEstado(
            $(this), 
            valor.length <= 255 ? 'ok' : 'error', 
            `La descripción no puede exceder los 255 caracteres (${valor.length}/255)`
        );
    });

    // Validación de Selects requeridos
    $(document).on('change', 'select[name="ID_Cliente"], select[name="ID_Tienda"], select[name="ID_Casillero"], select[name="ID_Sucursal"]', function() {
        const valor = $(this).val();
        const campo = $(this).attr('name').replace('ID_', '');
        mostrarEstado(
            $(this), 
            valor ? 'ok' : 'error', 
            `Debe seleccionar un ${campo.toLowerCase()}`
        );
    });

    // Validación de campos de consolidación cuando el estado es "Consolidado"
    $(document).on('change', 'select[name="ID_Categoria"], select[name="ID_Courier"]', function() {
        const $estadoSelect = $(this).closest('form').find('.estado-select');
        
        if ($estadoSelect.val() === 'Consolidado') {
            const valor = $(this).val();
            const campo = $(this).attr('name').replace('ID_', '');
            mostrarEstado(
                $(this), 
                valor ? 'ok' : 'error', 
                `Debe seleccionar un ${campo.toLowerCase()}`
            );
        }
    });

    // ===============================
    // FUNCIÓN DE VALIDACIÓN COMPLETA
    // ===============================
    window.validarFormularioPrealerta = function($form) {
        let errores = [];

        // Validar campos básicos
        const cliente = $form.find('[name="ID_Cliente"]').val();
        const tienda = $form.find('[name="ID_Tienda"]').val();
        const casillero = $form.find('[name="ID_Casillero"]').val();
        const sucursal = $form.find('[name="ID_Sucursal"]').val();
        const tracking = $form.find('[name="Tracking_Tienda"]').val().trim();
        const piezas = parseInt($form.find('[name="Prealerta_Piezas"]').val());
        const peso = parseFloat($form.find('[name="Prealerta_Peso"]').val());
        const descripcion = $form.find('[name="Prealerta_Descripcion"]').val().trim();
        const estado = $form.find('[name="Estado"]').val();

        if (!cliente) errores.push('Debe seleccionar un cliente');
        if (!tienda) errores.push('Debe seleccionar una tienda');
        if (!casillero) errores.push('Debe seleccionar un casillero');
        if (!sucursal) errores.push('Debe seleccionar una sucursal');
        
        if (!tracking || !/^[A-Za-z0-9\-]{3,50}$/.test(tracking)) {
            errores.push('El tracking debe contener entre 3 y 50 caracteres (letras, números o guiones)');
        }
        
        if (!piezas || piezas <= 0) {
            errores.push('Debe ingresar un número válido de piezas mayor que 0');
        }
        
        if (!peso || peso <= 0) {
            errores.push('Debe ingresar un peso válido mayor que 0');
        }
        
        if (descripcion.length > 255) {
            errores.push('La descripción no puede exceder los 255 caracteres');
        }

        // Validar campos de consolidación si el estado es "Consolidado"
        if (estado === 'Consolidado') {
            const categoria = $form.find('[name="ID_Categoria"]').val();
            const courier = $form.find('[name="ID_Courier"]').val();
            
            if (!categoria) errores.push('Debe seleccionar una categoría para consolidar');
            if (!courier) errores.push('Debe seleccionar un courier para consolidar');
        }

        // Mostrar errores si existen
        if (errores.length > 0) {
            Swal.fire({
                icon: 'warning',
                title: 'Campos inválidos',
                html: errores.map(e => `• ${e}`).join('<br>'),
                confirmButtonText: 'Entendido'
            });
            return false;
        }

        return true;
    };

    // ===============================
    // LIMPIAR VALIDACIONES AL CERRAR MODAL
    // ===============================
    $(document).on('hidden.bs.modal', '.modal', function() {
        $(this).find('.is-valid, .is-invalid').removeClass('is-valid is-invalid');
        $(this).find('.invalid-feedback').remove();
    });

    // ===============================
    // INICIALIZAR ESTADO DE CAMPOS CONSOLIDACIÓN
    // ===============================
    $(document).on('show.bs.modal', '.modal[id^="edit-prealerta-"]', function() {
        const $modal = $(this);
        const $estadoSelect = $modal.find('.estado-select');
        const $camposConsolidacion = $modal.find('.camposConsolidacion');
        
        // Mostrar u ocultar campos según el estado actual
        if ($estadoSelect.val() === 'Consolidado') {
            $camposConsolidacion.show();
            $camposConsolidacion.find('select').prop('required', true);
        } else {
            $camposConsolidacion.hide();
            $camposConsolidacion.find('select').prop('required', false);
        }
    });
});