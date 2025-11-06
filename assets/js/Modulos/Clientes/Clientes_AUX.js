// ===============================
// FUNCIONES DE VALIDACIÓN
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

function limpiarFormulario($form) {
    $form[0].reset();
    $form.find('.is-valid, .is-invalid').removeClass('is-valid is-invalid');
    $form.find('.invalid-feedback').remove();
}

function aplicarValidaciones($form) {
    // Remover eventos previos para evitar duplicados
    $form.find('input[name="Cedula_Identidad"]').off('input').on('input', function() {
        const regex = /^\d{6,23}$/;
        mostrarEstado($(this), regex.test($(this).val().trim()) ? 'ok' : 'error', 'Cédula inválida (6-23 dígitos)');
    });

    $form.find('input[name="Nombres_Cliente"], input[name="Apellidos_Cliente"]').off('input').on('input', function() {
        const regex = /^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/;
        mostrarEstado($(this), regex.test($(this).val().trim()) ? 'ok' : 'error', 'Solo letras y espacios');
    });

    $form.find('input[name="Correo_Cliente"]').off('input').on('input', function() {
        const regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        mostrarEstado($(this), regex.test($(this).val().trim()) ? 'ok' : 'error', 'Correo inválido');
    });

    $form.find('input[name="Telefono_Cliente"]').off('input').on('input', function() {
        const regex = /^\d{7,15}$/;
        mostrarEstado($(this), regex.test($(this).val().trim()) ? 'ok' : 'error', 'Teléfono inválido (7-15 dígitos)');
    });
}

// ============================================================
// FUNCIÓN PARA APLICAR COLORES A ÍCONOS
// ============================================================
function aplicarColoresIconos() {
    $('.table-actions a').each(function() {
        const color = $(this).data('color');
        if (color) {
            $(this).find('i').css('color', color);
        }
    });
}

// ============================================================
// LIMPIAR BACKDROPS AUTOMÁTICAMENTE
// ============================================================
$(document).on('hidden.bs.modal', '.modal', function() {
    // Esperar un momento para asegurarse de que el modal se cerró completamente
    setTimeout(function() {
        // Solo limpiar si NO hay otros modales abiertos
        if ($('.modal.show').length === 0) {
            $('.modal-backdrop').remove();
            $('body').removeClass('modal-open');
            $('body').css('padding-right', '');
        }
    }, 100);
});

// Limpiar SweetAlert cuando se cierra
$(document).on('click', '.swal2-confirm, .swal2-cancel', function() {
    setTimeout(function() {
        $('.swal2-container').remove();
    }, 100);
});

// ============================================================
// INICIALIZAR AL CARGAR PÁGINA
// ============================================================
$(document).ready(function() {
    // Aplicar validaciones al formulario de registro
    aplicarValidaciones($('#formRegistrarCliente'));
    
    // Aplicar colores a los íconos de acción
    aplicarColoresIconos();
    
    // Limpiar cualquier backdrop residual al cargar
    $('.modal-backdrop').remove();
    $('body').removeClass('modal-open');
    $('body').css('padding-right', '');
});

// ============================================================
// FUNCIONES GLOBALES
// ============================================================
window.setDeleteId = function(id) {
    $('#delete_cliente_id').val(id);
};