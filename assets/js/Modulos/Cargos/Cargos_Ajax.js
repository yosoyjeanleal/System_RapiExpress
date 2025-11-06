// ============================================================
// CARGO - GESTIÓN AJAX ADAPTADO A AJAX UTILS
// ============================================================

// 🧹 Limpiar formulario al cerrar modal de registro
$('#cargoModal').on('hidden.bs.modal', function () {
    AjaxUtils.limpiarFormularioModal($(this), '#formRegistrarCargo');
});

// ============================================================
// RECARGAR TABLA CARGOS
// ============================================================

function recargarTabla() {
    AjaxUtils.recargarTablaConModales({
        url: 'index.php?c=cargo&a=index',
        tableSelector: '#cargosTable',
        modalSelector: '.modal.fade[id^="edit-cargo-modal"]',
        onSuccess: () => {
            AjaxUtils.aplicarColoresIconos();
        },
        onError: () => {
            AjaxUtils.alertaError('No se pudo recargar la lista de cargos.');
        }
    });
}

// ============================================================
// REGISTRAR CARGO
// ============================================================

$('#formRegistrarCargo').on('submit', function (e) {
    e.preventDefault();
    const $form = $(this);

    AjaxUtils.enviarFormularioAjax({
        $form,
        modalId: 'cargoModal',
        onSuccess: function (respuesta) {
            AjaxUtils.alertaRespuesta(respuesta);
            if (respuesta.estado === 'success') recargarTabla();
        },
        onError: function () {
            AjaxUtils.alertaError('No se pudo registrar el cargo.');
        }
    });
});

// ============================================================
// EDITAR CARGO
// ============================================================

// Guardar valor original al abrir modal
$(document).on('show.bs.modal', '.modal[id^="edit-cargo-modal-"]', function () {
    const $modal = $(this);
    const $input = $modal.find('input[name="Cargo_Nombre"]');
    $input.data('original', $input.val().trim());
});

// Enviar cambios solo si hubo modificaciones
$(document).on('submit', 'form[id^="formEditarCargo-"]', function (e) {
    e.preventDefault();
    const $form = $(this);
    const $input = $form.find('input[name="Cargo_Nombre"]');
    const original = $input.data('original');
    const current = $input.val().trim();

    if (original === current) {
        AjaxUtils.alertaSinCambios();
        return;
    }

    AjaxUtils.enviarFormularioAjax({
        $form,
        modalId: $form.closest('.modal').attr('id'),
        onSuccess: function (respuesta) {
            AjaxUtils.alertaRespuesta(respuesta);
            if (respuesta.estado === 'success') recargarTabla();
        },
        onError: function () {
            AjaxUtils.alertaError('No se pudo actualizar el cargo.');
        }
    });
});

// ============================================================
// ELIMINAR CARGO
// ============================================================

$('#formEliminarCargo').on('submit', function (e) {
    e.preventDefault();

    AjaxUtils.enviarFormularioAjax({
        $form: $(this),
        modalId: 'delete-cargo-modal',
        onSuccess: function (respuesta) {
            AjaxUtils.alertaRespuesta(respuesta);
            if (respuesta.estado === 'success') recargarTabla();
        },
        onError: function () {
            AjaxUtils.alertaError('No se pudo eliminar el cargo.');
        }
    });
});

// ============================================================
// ASIGNAR ID A ELIMINAR
// ============================================================

function setDeleteId(id) {
    AjaxUtils.setDeleteId('delete_cargo_id', id);
}

// ============================================================
// DOCUMENT READY
// ============================================================

$(document).ready(function () {
    console.log('✅ cargo_ajax.js adaptado a AjaxUtils');
});
