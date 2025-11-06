// ============================================================
// REPARAR PANTALLA NEGRA DESPUÉS DE CERRAR SWEETALERT
// ============================================================
$(document).on('hidden.bs.modal', function () {
    // Si SweetAlert deja un overlay visible, lo eliminamos
    setTimeout(() => {
        $('.swal2-container').remove(); // limpia restos de SweetAlert
        $('.modal-backdrop').remove(); // limpia backdrop de Bootstrap
        $('body').removeClass('modal-open'); // repara scroll bloqueado
        $('body').css('padding-right', ''); // elimina padding del scrollbar
    }, 300);
});

// ============================================================
// RECARGAR TABLA CON MODALES
// ============================================================
function recargarTablaSucursales() {
    $.ajax({
        url: 'index.php?c=sucursal&a=index',
        type: 'GET',
        success: function (html) {
            // Reemplazar tbody
            const nuevoTbody = $(html).find('#sucursalesTable tbody').html();
            $('#sucursalesTable tbody').html(nuevoTbody);

            // Reemplazar modales de edición
            const nuevosModales = $(html).find('.modal.fade[id^="edit-sucursal-modal"]');
            $('.modal.fade[id^="edit-sucursal-modal"]').remove();
            $('body').append(nuevosModales);

            // Inicializar DataTable si aún no existe
            if (!$.fn.DataTable.isDataTable('#sucursalesTable')) {
                $('#sucursalesTable').DataTable({
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
            Swal.fire('Error', 'No se pudo recargar la lista de sucursales.', 'error');
        }
    });
}

// ============================================================
// REGISTRAR SUCURSAL
// ============================================================
$('#formRegistrarSucursal').on('submit', function (e) {
    e.preventDefault();
    const $form = $(this);
    const datos = new FormData(this);

    $.ajax({
        url: $form.attr('action'),
        type: 'POST',
        data: datos,
        contentType: false,
        processData: false,
        dataType: 'json',
        success: function (res) {
            $('#sucursalModal').modal('hide');

            setTimeout(() => {
                if (res.estado === 'success') {
                    $form[0].reset();
                    Swal.fire({
                        icon: 'success',
                        title: 'Éxito',
                        text: res.mensaje,
                        timer: 1500,
                        showConfirmButton: false
                    });
                    recargarTablaSucursales();
                } else {
                    Swal.fire({ icon: 'error', title: 'Error', text: res.mensaje });
                }
            }, 300);
        },
        error: function () {
            Swal.fire({ icon: 'error', title: 'Error', text: 'No se pudo registrar la sucursal.' });
        }
    });
});

// ============================================================
// DETECTAR CAMBIOS EN MODAL DE EDITAR
// ============================================================
let datosOriginalesEdicion = {};

// Guardar datos originales al abrir modal de edición
$(document).on('show.bs.modal', '.modal[id^="edit-sucursal-modal"]', function () {
    const $modal = $(this);
    const $form = $modal.find('form[id^="formEditarSucursal-"]');

    datosOriginalesEdicion = {};
    $form.find('input, select, textarea').each(function () {
        const name = $(this).attr('name');
        if (name) datosOriginalesEdicion[name] = $(this).val();
    });
});

// Verificar cambios antes de enviar
$(document).on('submit', 'form[id^="formEditarSucursal-"]', function (e) {
    e.preventDefault();
    const $form = $(this);

    let hayCambios = false;
    $form.find('input, select, textarea').each(function () {
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
        url: $form.attr('action'),
        type: 'POST',
        data: datos,
        contentType: false,
        processData: false,
        dataType: 'json',
        success: function (res) {
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
                    recargarTablaSucursales();
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: res.mensaje
                    });
                }
            }, 300);
        },
        error: function () {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'No se pudo actualizar la sucursal.'
            });
        }
    });
});

// Limpiar datos al cerrar modal
$(document).on('hidden.bs.modal', '.modal[id^="edit-sucursal-modal"]', function () {
    datosOriginalesEdicion = {};
});

// ============================================================
// ELIMINAR SUCURSAL
// ============================================================
$('#formEliminarSucursal').on('submit', function (e) {
    e.preventDefault();
    const $form = $(this);

    $.ajax({
        url: $form.attr('action'),
        type: 'POST',
        data: $form.serialize(),
        dataType: 'json',
        success: function (res) {
            $('#delete-sucursal-modal').modal('hide');

            setTimeout(() => {
                if (res.estado === 'success') {
                    Swal.fire({
                        icon: 'success',
                        title: 'Eliminado',
                        text: res.mensaje,
                        timer: 1500,
                        showConfirmButton: false
                    });
                    recargarTablaSucursales();
                } else {
                    Swal.fire({ icon: 'error', title: 'Error', text: res.mensaje });
                }
            }, 300);
        },
        error: function () {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'No se puede eliminar la sucursal porque está asociada a registros relacionados.'
            });
        }
    });
});

// ============================================================
// SET ID PARA ELIMINAR
// ============================================================
window.setDeleteId = function (id) {
    $('#delete_sucursal_id').val(id);
};
