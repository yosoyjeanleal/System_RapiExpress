// Validar "Cargo_Nombre" en tiempo real
$(document).on('input', 'input[name="Cargo_Nombre"]', function () {
    const $input = $(this);
    const valor = $input.val().trim();
    const regex = /^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]{1,20}$/;
    const maxLength = 20; // define maxLength

    let $mensaje = $input.next('.invalid-feedback');
    if ($mensaje.length === 0) {
        $mensaje = $('<div class="invalid-feedback"></div>');
        $input.after($mensaje);
    }
      

    if (valor === '') {
        $input.addClass('is-invalid').removeClass('is-valid');
        $mensaje.text('⚠️ El nombre del cargo es obligatorio.');
    } else if (!regex.test(valor)) {
        $input.addClass('is-invalid').removeClass('is-valid');
        $mensaje.text('❌ Solo letras y espacios (máx. 20 caracteres).');
    } else {
        $input.addClass('is-valid').removeClass('is-invalid');
        $mensaje.text('');
    }
});
