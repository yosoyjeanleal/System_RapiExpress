/**
 * ============================================================
 * AJAX CASILLERO - Adaptado al sistema AJAX UTILS
 * ============================================================
 * Usa funciones modulares globales de ajax_utils.js
 */

$(document).ready(function () {

    // ============================================================
    // RECARGAR TABLA DINÁMICAMENTE
    // ============================================================
    function recargarTabla() {
        AjaxUtils.recargarTablaConModales({
            url: 'index.php?c=casillero&a=index',
            tableSelector: '#casillerosTable',
            modalSelector: '.modal.fade[id^="edit-casillero-modal"]',
            mensajeError: 'No se pudo recargar la lista de casilleros.'
        });
    }

    // ============================================================
    // LIMPIEZA GLOBAL DE BACKDROPS
    // ============================================================
    $(document).on('hidden.bs.modal', '.modal', function () {
        AjaxUtils.limpiarBackdrop();
    });

    // ============================================================
    // LIMPIEZA AL CERRAR MODAL DE REGISTRO
    // ============================================================
    $('#casilleroModal').on('hidden.bs.modal', function () {
        AjaxUtils.limpiarFormularioModal($(this), '#formRegistrarCasillero');
    });

    // ============================================================
    // GUARDAR VALORES ORIGINALES AL ABRIR MODAL EDICIÓN
    // ============================================================
    $(document).on('show.bs.modal', '[id^="edit-casillero-modal-"]', function () {
        const $modal = $(this);
        const $nombre = $modal.find('input[name="Casillero_Nombre"]');
        const $direccion = $modal.find('input[name="Direccion"]');

        $nombre.data('original', $nombre.val().trim());
        $direccion.data('original', $direccion.val().trim());
    });

    // ============================================================
    // SUBMIT: REGISTRAR CASILLERO
    // ============================================================
    $('#formRegistrarCasillero').on('submit', function (e) {
        e.preventDefault();

        const $form = $(this);
        const $nombre = $form.find('input[name="Casillero_Nombre"]');
        const $direccion = $form.find('input[name="Direccion"]');
        const nombre = $nombre.val().trim();
        const direccion = $direccion.val().trim();

        // Validaciones reutilizando utils
        const rNombre = AjaxUtils.validarNombreCampo(nombre);
        const rDir = AjaxUtils.validarDireccionCampo(direccion);

        if (!rNombre.ok) AjaxUtils.markInvalid($nombre, rNombre.msg);
        else AjaxUtils.markValid($nombre);

        if (!rDir.ok) AjaxUtils.markInvalid($direccion, rDir.msg);
        else AjaxUtils.markValid($direccion);

        if ($form.find('.is-invalid').length) {
            AjaxUtils.firstInvalidFocus($form);
            AjaxUtils.alertaAdvertencia('Corrige los campos marcados.');
            return;
        }

        AjaxUtils.enviarFormularioAjax({
            $form: $form,
            modalId: 'casilleroModal',
            onSuccess: function (r) {
                AjaxUtils.alertaRespuesta(r);
                if (r.estado === 'success') {
                    AjaxUtils.limpiarFormulario($form);
                    recargarTabla();
                }
            }
        });
    });

    // ============================================================
    // SUBMIT: EDITAR CASILLERO
    // ============================================================
    $(document).on('submit', '.formEditarCasillero', function (e) {
        e.preventDefault();

        const $form = $(this);
        const $nombre = $form.find('input[name="Casillero_Nombre"]');
        const $direccion = $form.find('input[name="Direccion"]');

        const nombreOriginal = $nombre.data('original');
        const direccionOriginal = $direccion.data('original');
        const nombreActual = $nombre.val().trim();
        const direccionActual = $direccion.val().trim();

        if (nombreOriginal === nombreActual && direccionOriginal === direccionActual) {
            AjaxUtils.alertaSinCambios();
            return;
        }

        const rNombre = AjaxUtils.validarNombreCampo(nombreActual);
        const rDir = AjaxUtils.validarDireccionCampo(direccionActual);

        if (!rNombre.ok) AjaxUtils.markInvalid($nombre, rNombre.msg);
        else AjaxUtils.markValid($nombre);

        if (!rDir.ok) AjaxUtils.markInvalid($direccion, rDir.msg);
        else AjaxUtils.markValid($direccion);

        if ($form.find('.is-invalid').length) {
            AjaxUtils.firstInvalidFocus($form);
            AjaxUtils.alertaAdvertencia('Corrige los campos marcados.');
            return;
        }

        AjaxUtils.enviarFormularioAjax({
            $form: $form,
            modalId: $form.closest('.modal').attr('id'),
            onSuccess: function (r) {
                AjaxUtils.alertaRespuesta(r);
                if (r.estado === 'success') {
                    recargarTabla();
                }
            }
        });
    });

    // ============================================================
    // SUBMIT: ELIMINAR CASILLERO
    // ============================================================
    $('#formEliminarCasillero').on('submit', function (e) {
        e.preventDefault();

        const $form = $(this);
        AjaxUtils.enviarFormularioAjax({
            $form: $form,
            modalId: 'delete-casillero-modal',
            onSuccess: function (r) {
                AjaxUtils.alertaRespuesta(r);
                if (r.estado === 'success') recargarTabla();
            }
        });
    });

    // ============================================================
    // ASIGNAR ID A ELIMINAR
    // ============================================================
    window.setDeleteId = function (id) {
        AjaxUtils.setDeleteId('delete_casillero_id', id);
    };

    console.log('✅ ajax_casillero.js inicializado con AjaxUtils');
});
