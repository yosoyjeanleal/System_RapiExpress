// ============================================================
// VALIDACIONES
// ============================================================
const campos = {
    nombre_tienda: 'texto',
    direccion_tienda: 'texto',
    telefono_tienda: 'telefono',
    correo_tienda: 'correo'
};

function validarCampo($input, tipo) {
    const valor = $input.val().trim();
    let valido = false;

    switch (tipo) {
        case 'texto':
            valido = valor.length > 0;
            break;
        case 'telefono':
            valido = /^\+?\d{7,20}$/.test(valor);
            break;
        case 'correo':
            valido = /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(valor);
            break;
    }

    $input.removeClass('is-invalid is-valid');
    if (valido) $input.addClass('is-valid');
    else $input.addClass('is-invalid');

    return valido;
}

function validarFormulario($form) {
    let valido = true;
    $form.find('input[name]').each(function () {
        const name = $(this).attr('name');
        if (campos[name]) valido &= validarCampo($(this), campos[name]);
    });
    return Boolean(valido);
}

$(document).on('input', 'input[name]', function () {
    const name = $(this).attr('name');
    if (campos[name]) validarCampo($(this), campos[name]);
});
