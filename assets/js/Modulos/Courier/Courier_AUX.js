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
// VALIDACIONES DINÁMICAS - MODAL REGISTRAR
// ===============================
$('#formRegistrarCourier input[name="RIF_Courier"]').on('input', function() {
    const regex = /^[JGVEP]-\d{8}-\d$/;
    mostrarEstado($(this), regex.test($(this).val().trim()) ? 'ok' : 'error', 'Formato RIF inválido (Ej: J-12345678-9)');
});

$('#formRegistrarCourier input[name="Courier_Nombre"]').on('input', function() {
    const regex = /^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]{3,50}$/;
    mostrarEstado($(this), regex.test($(this).val().trim()) ? 'ok' : 'error', 'Solo letras y espacios (3-50 caracteres)');
});

$('#formRegistrarCourier input[name="Courier_Direccion"]').on('input', function() {
    const regex = /^[a-zA-Z0-9\sáéíóúÁÉÍÓÚñÑ.,\-()_#]{5,150}$/;
    mostrarEstado($(this), regex.test($(this).val().trim()) ? 'ok' : 'error', 'Dirección inválida (5-150 caracteres)');
});

$('#formRegistrarCourier input[name="Courier_Telefono"]').on('input', function() {
    const regex = /^(\+?\d{1,3})?\d{7,15}$/;
    mostrarEstado($(this), regex.test($(this).val().trim()) ? 'ok' : 'error', 'Teléfono inválido (Ej: 04121234567 o +584121234567)');
});

$('#formRegistrarCourier input[name="Courier_Correo"]').on('input', function() {
    const regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    mostrarEstado($(this), regex.test($(this).val().trim()) ? 'ok' : 'error', 'Correo electrónico inválido');
});

// ===============================
// VALIDACIONES DINÁMICAS - MODALES EDITAR
// ===============================
$(document).on('input', 'form[id^="formEditarCourier-"] input[name="RIF_Courier"]', function() {
    const regex = /^[JGVEP]-\d{8}-\d$/;
    mostrarEstado($(this), regex.test($(this).val().trim()) ? 'ok' : 'error', 'Formato RIF inválido (Ej: J-12345678-9)');
});

$(document).on('input', 'form[id^="formEditarCourier-"] input[name="Courier_Nombre"]', function() {
    const regex = /^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]{3,50}$/;
    mostrarEstado($(this), regex.test($(this).val().trim()) ? 'ok' : 'error', 'Solo letras y espacios (3-50 caracteres)');
});

$(document).on('input', 'form[id^="formEditarCourier-"] input[name="Courier_Direccion"]', function() {
    const regex = /^[a-zA-Z0-9\sáéíóúÁÉÍÓÚñÑ.,\-()_#]{5,150}$/;
    mostrarEstado($(this), regex.test($(this).val().trim()) ? 'ok' : 'error', 'Dirección inválida (5-150 caracteres)');
});

$(document).on('input', 'form[id^="formEditarCourier-"] input[name="Courier_Telefono"]', function() {
    const regex = /^(\+?\d{1,3})?\d{7,15}$/;
    mostrarEstado($(this), regex.test($(this).val().trim()) ? 'ok' : 'error', 'Teléfono inválido (Ej: 04121234567 o +584121234567)');
});

$(document).on('input', 'form[id^="formEditarCourier-"] input[name="Courier_Correo"]', function() {
    const regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    mostrarEstado($(this), regex.test($(this).val().trim()) ? 'ok' : 'error', 'Correo electrónico inválido');
});

// ===============================
// LIMPIAR VALIDACIONES AL CERRAR MODAL REGISTRAR
// ===============================
$('#courierModal').on('hidden.bs.modal', function () {
    const $form = $('#formRegistrarCourier');
    $form[0].reset();
    $form.find('.is-valid, .is-invalid').removeClass('is-valid is-invalid');
    $form.find('.invalid-feedback').remove();
});

// ===============================
// LIMPIAR VALIDACIONES AL CERRAR MODALES EDITAR
// ===============================
$(document).on('hidden.bs.modal', '.modal[id^="edit-courier-modal"]', function () {
    const $form = $(this).find('form');
    $form.find('.is-valid, .is-invalid').removeClass('is-valid is-invalid');
    $form.find('.invalid-feedback').remove();
});