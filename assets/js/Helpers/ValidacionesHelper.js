document.querySelector('select[name="sucursal"]').addEventListener('change', function () {
    const telefonoInput = document.querySelector('input[name="telefono"]');
    const sucursal = this.value;

    if (sucursal === 'sucursal_usa') {
        telefonoInput.pattern = "^\\+1\\s*\\d{3}\\s*\\d{3}\\s*\\d{4}$";
        telefonoInput.placeholder = "+1 212 555 1234";
        telefonoInput.title = "Formato esperado: +1 212 555 1234 (puede ir con o sin espacios)";
    } else if (sucursal === 'sucursal_ven') {
        telefonoInput.pattern = "^\\+58\\s*\\d{3}\\s*\\d{7}$";
        telefonoInput.placeholder = "+58 212 5551234";
        telefonoInput.title = "Formato esperado: +58 212 5551234 (puede ir con o sin espacios)";
    } else if (sucursal === 'sucursal_ec') {
        telefonoInput.pattern = "^\\+593\\s*9\\s*\\d{8}$";
        telefonoInput.placeholder = "+593 9 87654321";
        telefonoInput.title = "Formato esperado: +593 9 87654321 (puede ir con o sin espacios)";
    } else {
        telefonoInput.removeAttribute("pattern");
        telefonoInput.placeholder = "Teléfono";
        telefonoInput.title = "";
    }
});

window.addEventListener("load", () => {
      document.body.classList.remove("loading");
      document.body.classList.add("loaded");
    });