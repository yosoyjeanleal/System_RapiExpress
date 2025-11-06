// ============================================================
// VALIDACIONES Y UTILIDADES AUXILIARES
// ============================================================

// Limpiar modales y backdrop al cerrar
$(document).on('hidden.bs.modal', '.modal', limpiarBackdrop);

// Guardar valores originales al abrir modal de edición
$(document).on('show.bs.modal', '[id^="edit-categoria-modal-"]', function () {
    $(this).find('input').each(function () {
        $(this).data('original', $(this).val());
    });
});

// Helpers de validación
function ensureFeedback($input) {
    let $fb = $input.siblings('.invalid-feedback');
    if ($fb.length === 0) {
        $fb = $('<div class="invalid-feedback"></div>');
        $input.after($fb);
    }
    return $fb;
}

function markInvalid($input, message) {
    const $fb = ensureFeedback($input);
    $input.addClass('is-invalid').removeClass('is-valid');
    $fb.text(message).show();
}

function markValid($input) {
    const $fb = ensureFeedback($input);
    $input.addClass('is-valid').removeClass('is-invalid');
    $fb.text('').hide();
}

function clearValidation($input) {
    const $fb = ensureFeedback($input);
    $input.removeClass('is-valid is-invalid');
    $fb.text('').hide();
}

// Reglas de validación
const regexNombre = /^[A-Za-z0-9\sáéíóúÁÉÍÓÚñÑ.,\-()]{3,50}$/;

function validarNombreCampo(value) {
    if (!value) return { ok: false, msg: 'El nombre es obligatorio.' };
    if (value.length < 3) return { ok: false, msg: 'Mínimo 3 caracteres.' };
    if (value.length > 50) return { ok: false, msg: 'Máximo 50 caracteres.' };
    if (!regexNombre.test(value)) return { ok: false, msg: 'Solo letras, números y (,.-()).' };
    return { ok: true };
}

function validarNumeroCampo(value, min = 0) {
    if (value === '' || isNaN(value)) return { ok: false, msg: 'Debe ser un número válido.' };
    if (parseFloat(value) < min) return { ok: false, msg: `Debe ser ≥ ${min}` };
    return { ok: true };
}

// Validación en tiempo real
$(document).on('input', 'input[name="nombre"]', function () {
    const val = $(this).val().trim();
    const res = validarNombreCampo(val);
    res.ok ? markValid($(this)) : markInvalid($(this), res.msg);
});

$(document).on('input', 'input[name="precio"], input[name="altura"], input[name="largo"], input[name="ancho"], input[name="peso"], input[name="piezas"]', function () {
    const min = $(this).attr('name') === 'piezas' ? 1 : 0;
    const res = validarNumeroCampo($(this).val(), min);
    res.ok ? markValid($(this)) : markInvalid($(this), res.msg);
});
