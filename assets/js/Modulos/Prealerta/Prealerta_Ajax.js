$(document).ready(function() {

    // ============================================================
    // REPARAR PANTALLA NEGRA DESPUÉS DE CERRAR MODAL/SWEETALERT
    // ============================================================
    $(document).on('hidden.bs.modal', function () {
        setTimeout(() => {
            $('.swal2-container').remove();
            $('.modal-backdrop').remove();
            $('body').removeClass('modal-open');
            $('body').css('padding-right', '');
        }, 300);
    });

    // ============================================================
    // RECARGAR TABLA CON MODALES
    // ============================================================
    function recargarTablaPrealertas() {
        $.ajax({
            url: 'index.php?c=prealerta&a=index',
            type: 'GET',
            success: function (html) {
                const $html = $(html);

                // Reemplazar tbody
                const nuevoTbody = $html.find('table.data-table tbody').html();
                $('table.data-table tbody').html(nuevoTbody);

                // Reemplazar modales de edición
                const nuevosModales = $html.find('.modal.fade[id^="edit-prealerta-"]');
                $('.modal.fade[id^="edit-prealerta-"]').remove();
                $('body').append(nuevosModales);

                // Inicializar DataTable si aún no existe
                if (!$.fn.DataTable.isDataTable('table.data-table')) {
                    $('table.data-table').DataTable({
                        responsive: true,
                        autoWidth: false,
                        language: { url: 'assets/Temple/src/plugins/datatables/js/es_es.json' },
                        columnDefs: [{ targets: 'datatable-nosort', orderable: false }]
                    });
                }

                // Restaurar colores de íconos
                $('.table-actions a').each(function () {
                    const color = $(this).data('color');
                    if (color) $(this).find('i').css('color', color);
                });
            },
            error: function () {
                Swal.fire('Error', 'No se pudo recargar la lista de prealertas.', 'error');
            }
        });
    }

    // ============================================================
    // MOSTRAR CAMPOS CUANDO SE SELECCIONA "CONSOLIDADO"
    // ============================================================
    $(document).on('change', '.estado-select', function() {
        const $form = $(this).closest('form');
        const $camposConsolidacion = $form.find('.camposConsolidacion');
        
        if ($(this).val() === 'Consolidado') {
            $camposConsolidacion.show();
            $camposConsolidacion.find('select').prop('required', true);
        } else {
            $camposConsolidacion.hide();
            $camposConsolidacion.find('select').prop('required', false);
            // Limpiar valores cuando se oculta
            $camposConsolidacion.find('select').val('');
        }
    });

    // ============================================================
    // REGISTRAR PREALERTA
    // ============================================================
    $('#form-registrar-prealerta').on('submit', function(e) {
        e.preventDefault();
        const $form = $(this);

        if (!validarFormularioPrealerta($form)) return;

        const datos = new FormData(this);

        $.ajax({
            url: 'index.php?c=prealerta&a=registrar',
            type: 'POST',
            data: datos,
            contentType: false,
            processData: false,
            dataType: 'json',
            success: function(res) {
                $('#prealertaModal').modal('hide');

                setTimeout(() => {
                    if (res.estado === 'success') {
                        $form[0].reset();
                        $form.find('.is-valid, .is-invalid').removeClass('is-valid is-invalid');
                        Swal.fire({
                            icon: 'success',
                            title: 'Éxito',
                            text: res.mensaje,
                            timer: 1500,
                            showConfirmButton: false
                        });
                        recargarTablaPrealertas();
                    } else {
                        Swal.fire({ icon: 'error', title: 'Error', text: res.mensaje });
                    }
                }, 300);
            },
            error: function() {
                Swal.fire({ icon: 'error', title: 'Error', text: 'No se pudo registrar la prealerta.' });
            }
        });
    });

    // ============================================================
    // DETECTAR CAMBIOS EN MODAL DE EDITAR
    // ============================================================
    let datosOriginalesEdicion = {};

    // Guardar datos originales al abrir modal de edición
    $(document).on('show.bs.modal', '.modal[id^="edit-prealerta-"]', function () {
        const $modal = $(this);
        const $form = $modal.find('.form-editar-prealerta');

        datosOriginalesEdicion = {};
        $form.find('.campo-editable').each(function () {
            const name = $(this).attr('name');
            if (name) datosOriginalesEdicion[name] = $(this).val();
        });
    });

    // ============================================================
    // EDITAR PREALERTA
    // ============================================================
    $(document).on('submit', '.form-editar-prealerta', function(e) {
        e.preventDefault();
        const $form = $(this);

        if (!validarFormularioPrealerta($form)) return;

        // Verificar cambios SOLO en campos editables
        let hayCambios = false;
        $form.find('.campo-editable').each(function () {
            const name = $(this).attr('name');
            if (name && datosOriginalesEdicion[name] !== $(this).val()) {
                hayCambios = true;
                return false;
            }
        });

        if (!hayCambios) {
            Swal.fire({
                icon: 'info',
                title: 'Sin cambios',
                text: 'No se detectaron modificaciones en los datos.',
                timer: 2000,
                showConfirmButton: false
            });
            return;
        }

        const datos = new FormData(this);

        $.ajax({
            url: 'index.php?c=prealerta&a=editar',
            type: 'POST',
            data: datos,
            contentType: false,
            processData: false,
            dataType: 'json',
            success: function(res) {
                $form.closest('.modal').modal('hide');

                setTimeout(() => {
                    if (res.estado === 'success') {
                        Swal.fire({
                            icon: 'success',
                            title: 'Actualizado',
                            text: res.mensaje,
                            timer: 1500,
                            showConfirmButton: false
                        });
                        recargarTablaPrealertas();
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: res.mensaje
                        });
                    }
                }, 300);
            },
            error: function() {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'No se pudo actualizar la prealerta.'
                });
            }
        });
    });

    // Limpiar datos al cerrar modal
    $(document).on('hidden.bs.modal', '.modal[id^="edit-prealerta-"]', function () {
        datosOriginalesEdicion = {};
    });

    // ============================================================
    // ELIMINAR PREALERTA
    // ============================================================
    $('#form-eliminar-prealerta').on('submit', function(e) {
        e.preventDefault();
        const $form = $(this);

        $.ajax({
            url: 'index.php?c=prealerta&a=eliminar',
            type: 'POST',
            data: $form.serialize(),
            dataType: 'json',
            success: function(res) {
                $('#delete-prealerta-modal').modal('hide');

                setTimeout(() => {
                    if (res.estado === 'success') {
                        Swal.fire({
                            icon: 'success',
                            title: 'Eliminado',
                            text: res.mensaje,
                            timer: 1500,
                            showConfirmButton: false
                        });
                        recargarTablaPrealertas();
                    } else {
                        Swal.fire({ icon: 'error', title: 'Error', text: res.mensaje });
                    }
                }, 300);
            },
            error: function() {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'No se pudo eliminar la prealerta. Puede estar asociada a otros registros.'
                });
            }
        });
    });

    // ============================================================
    // SET ID PARA ELIMINAR
    // ============================================================
    window.setDeletePrealertaId = function (id) {
        $('#delete_prealerta_id').val(id);
    };
});