// ============================================================
// ASIGNAR ID PARA ELIMINAR
// ============================================================
function setDeleteId(id) {
    document.getElementById('delete_tienda_id').value = id;
}

// ============================================================
// RECARGAR TABLA DINÁMICAMENTE (ESTILO CASILLERO)
// ============================================================
function recargarTabla() {
    if ($.fn.DataTable.isDataTable('#tiendasTable')) {
        $('#tiendasTable').DataTable().destroy();
    }

    $.ajax({
        url: 'index.php?c=tienda&a=index',
        type: 'GET',
        success: function (html) {
            const nuevoTbody = $(html).find('#tiendasTable tbody').html();
            const nuevosModales = $(html).find('.modal.fade[id^="edit-tienda-modal"]');

            $('#tiendasTable tbody').html(nuevoTbody);

            $('.modal.fade[id^="edit-tienda-modal"]').remove();
            $('body').append(nuevosModales);

            $('#tiendasTable').DataTable({
                destroy: true,
                responsive: true,
                autoWidth: false,
                language: {
                    url: 'assets/Temple/src/plugins/datatables/js/es_es.json'
                },
                columnDefs: [
                    { targets: 'datatable-nosort', orderable: false }
                ]
            });

            // Restaurar colores de íconos
            $('.table-actions a').each(function () {
                const color = $(this).data('color');
                if (color) $(this).find('i').css('color', color);
            });
        },
        error: function () {
            Swal.fire('Error', 'No se pudo recargar la lista de tiendas.', 'error');
        }
    });
}

// ============================================================
// LIMPIEZA GLOBAL DE BACKDROPS
// ============================================================
$(document).on('hidden.bs.modal', '.modal', function () {
    $('body').removeClass('modal-open');
    $('.modal-backdrop').remove();
});

// ============================================================
// LIMPIAR FORMULARIO DE REGISTRO AL CERRAR MODAL
// ============================================================
$('#tiendaModal').on('hidden.bs.modal', function () {
    const $form = $('#formRegistrarTienda');
    $form[0].reset();
    $form.find('input').removeClass('is-valid is-invalid');
    $('.modal-backdrop').remove();
    $('body').removeClass('modal-open');
});

// ============================================================
// GUARDAR VALORES ORIGINALES AL ABRIR MODAL EDICIÓN
// ============================================================
$(document).on('show.bs.modal', '[id^="edit-tienda-modal-"]', function () {
    const $form = $(this).find('form');
    $form.data('original', $form.serialize());
});

// ============================================================
// SUBMIT: REGISTRAR TIENDA
// ============================================================
$('#formRegistrarTienda').on('submit', function (e) {
    e.preventDefault();
    const $form = $(this);

    if (!validarFormulario($form)) {
        Swal.fire('Error', 'Corrige los campos inválidos', 'error');
        return;
    }

    $.ajax({
        url: 'index.php?c=tienda&a=registrar',
        type: 'POST',
        data: $form.serialize(),
        dataType: 'json',
        success: function (r) {
            $('#tiendaModal').modal('hide');

            Swal.fire({
                icon: r.estado === 'success' ? 'success' : 'error',
                title: r.estado === 'success' ? 'Éxito' : 'Error',
                text: r.mensaje,
                timer: r.estado === 'success' ? 1500 : 2500,
                showConfirmButton: r.estado !== 'success'
            });

            if (r.estado === 'success') {
                $form[0].reset();
                $form.find('input').removeClass('is-valid is-invalid');
                recargarTabla();
            }
        },
        error: function () {
            Swal.fire('Error', 'No se pudo registrar la tienda.', 'error');
        }
    });
});

// ============================================================
// SUBMIT: EDITAR TIENDA
// ============================================================
$(document).on('submit', '.formEditarTienda', function (e) {
    e.preventDefault();
    const $form = $(this);

    if (!validarFormulario($form)) {
        Swal.fire('Error', 'Corrige los campos inválidos', 'error');
        return;
    }

    // Detectar cambios
    const original = $form.data('original');
    const actual = $form.serialize();
    if (original === actual) {
        Swal.fire({
            icon: 'info',
            title: 'Sin cambios',
            text: 'No se detectaron modificaciones.'
        });
        return;
    }

    $.ajax({
        url: 'index.php?c=tienda&a=editar',
        type: 'POST',
        data: $form.serialize(),
        dataType: 'json',
        success: function (r) {
            const modal = $form.closest('.modal');
            modal.modal('hide');

            modal.one('hidden.bs.modal', function () {
                $('.modal-backdrop').remove();
                $('body').removeClass('modal-open');

                Swal.fire({
                    icon: r.estado === 'success' ? 'success' : 'error',
                    title: r.estado === 'success' ? 'Actualizado' : 'Error',
                    text: r.mensaje,
                    timer: r.estado === 'success' ? 1500 : 2500,
                    showConfirmButton: r.estado !== 'success'
                });

                if (r.estado === 'success') recargarTabla();
            });
        },
        error: function () {
            Swal.fire('Error', 'No se pudo actualizar la tienda.', 'error');
        }
    });
});


            // ============================================================
            // ELIMINAR TIENDA (CORREGIDO)
            // ============================================================
            $('#formEliminarTienda').on('submit', function(e) {
                e.preventDefault();
                const id = $('#delete_tienda_id').val();

                if (!id) {
                    Swal.fire('Error', 'ID de tienda no definido', 'error');
                    return;
                }

                $.post('index.php?c=tienda&a=eliminar', {
                    id_tienda: id
                }, function(respuesta) {
                    if (respuesta.estado === 'success') {
                        $('#delete-tienda-modal').modal('hide');
                        Swal.fire({
                            icon: 'success',
                            title: 'Eliminado',
                            text: respuesta.mensaje,
                            timer: 2000,
                            showConfirmButton: false,
                            timerProgressBar: true
                        });
                        // ✅ USAR recargarTabla() en lugar de location.reload()
                        recargarTabla();
                    } else {
                        Swal.fire('Error', respuesta.mensaje, 'error');
                    }
                }, 'json').fail(function() {
                    Swal.fire('Error', 'No se pudo conectar con el servidor', 'error');
                });
            });

