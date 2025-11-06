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
function recargarTablaCouriers() {
    $.ajax({
        url: 'index.php?c=courier&a=index',
        type: 'GET',
        success: function (html) {
            // Reemplazar tbody
            const nuevoTbody = $(html).find('#couriersTable tbody').html();
            $('#couriersTable tbody').html(nuevoTbody);

            // Reemplazar modales de edición
            const nuevosModales = $(html).find('.modal.fade[id^="edit-courier-modal"]');
            $('.modal.fade[id^="edit-courier-modal"]').remove();
            $('body').append(nuevosModales);

            // Inicializar DataTable si aún no existe
            if (!$.fn.DataTable.isDataTable('#couriersTable')) {
                $('#couriersTable').DataTable({
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
            Swal.fire('Error', 'No se pudo recargar la lista de couriers.', 'error');
        }
    });
}

// ============================================================
// REGISTRAR COURIER
// ============================================================
$('#formRegistrarCourier').on('submit', function (e) {
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
            $('#courierModal').modal('hide');

            setTimeout(() => {
                if (res.estado === 'success') {
                    $form[0].reset();
                    $form.find('.is-valid, .is-invalid').removeClass('is-valid is-invalid');
                    $form.find('.invalid-feedback').remove();
                    
                    Swal.fire({
                        icon: 'success',
                        title: 'Éxito',
                        text: res.mensaje,
                        timer: 1500,
                        showConfirmButton: false
                    });
                    recargarTablaCouriers();
                } else {
                    Swal.fire({ icon: 'error', title: 'Error', text: res.mensaje });
                }
            }, 300);
        },
        error: function (xhr) {
            $('#courierModal').modal('hide');
            $('.modal-backdrop').remove();
            $('body').removeClass('modal-open');
            $('body').css('padding-right', '');
            
            setTimeout(() => {
                let mensaje = 'No se pudo registrar el courier.';
                try {
                    const res = JSON.parse(xhr.responseText);
                    mensaje = res.mensaje || mensaje;
                } catch (e) {
                    // Si no se puede parsear, usar mensaje por defecto
                }
                Swal.fire({ icon: 'error', title: 'Error', text: mensaje });
            }, 300);
        }
    });
});

// ============================================================
// DETECTAR CAMBIOS EN MODAL DE EDITAR
// ============================================================
let datosOriginalesEdicion = {};

// Guardar datos originales al abrir modal de edición
$(document).on('show.bs.modal', '.modal[id^="edit-courier-modal"]', function () {
    const $modal = $(this);
    const $form = $modal.find('form[id^="formEditarCourier-"]');

    datosOriginalesEdicion = {};
    $form.find('input, select, textarea').each(function () {
        const name = $(this).attr('name');
        if (name) datosOriginalesEdicion[name] = $(this).val();
    });
});

// Verificar cambios antes de enviar
$(document).on('submit', 'form[id^="formEditarCourier-"]', function (e) {
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
                    recargarTablaCouriers();
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
                text: 'No se pudo actualizar el courier.'
            });
        }
    });
});

// Limpiar datos al cerrar modal
$(document).on('hidden.bs.modal', '.modal[id^="edit-courier-modal"]', function () {
    datosOriginalesEdicion = {};
});

// ============================================================
// ELIMINAR COURIER
// ============================================================
$('#formEliminarCourier').on('submit', function (e) {
    e.preventDefault();
    const $form = $(this);

    $.ajax({
        url: $form.attr('action'),
        type: 'POST',
        data: $form.serialize(),
        dataType: 'json',
        success: function (res) {
            $('#delete-courier-modal').modal('hide');

            setTimeout(() => {
                if (res.estado === 'success') {
                    Swal.fire({
                        icon: 'success',
                        title: 'Eliminado',
                        text: res.mensaje,
                        timer: 1500,
                        showConfirmButton: false
                    });
                    recargarTablaCouriers();
                } else {
                    Swal.fire({ icon: 'error', title: 'Error', text: res.mensaje });
                }
            }, 300);
        },
        error: function () {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'No se puede eliminar el courier porque está asociado a paquetes.'
            });
        }
    });
});

// ============================================================
// SET ID PARA ELIMINAR
// ============================================================
window.setDeleteId = function (id) {
    $('#delete_courier_id').val(id);
};