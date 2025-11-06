// ============================================================
// RECARGAR TABLA DINÁMICAMENTE
// ============================================================
function recargarTabla() {
    if ($.fn.DataTable.isDataTable('#categoriasTable')) {
        $('#categoriasTable').DataTable().destroy();
    }

    $.ajax({
        url: 'index.php?c=categoria&a=index',
        type: 'GET',
        success: function (html) {
            const nuevoTbody = $(html).find('#categoriasTable tbody').html();
            const nuevosModales = $(html).find('.modal.fade[id^="edit-categoria-modal"]');

            $('#categoriasTable tbody').html(nuevoTbody);
            $('.modal.fade[id^="edit-categoria-modal"]').remove();
            $('body').append(nuevosModales);

            $('#categoriasTable').DataTable({
                responsive: true,
                autoWidth: false,
                language: { url: 'assets/Temple/src/plugins/datatables/js/es_es.json' },
                columnDefs: [{ targets: 'datatable-nosort', orderable: false }]
            });

            // Restaurar colores de íconos
            $('.table-actions a').each(function () {
                const color = $(this).data('color');
                if (color) $(this).find('i').css('color', color);
            });
        },
        error: function () {
            Swal.fire('Error', 'No se pudo recargar la lista de categorías.', 'error');
        }
    });
}

// ============================================================
// REGISTRO DE CATEGORÍA
// ============================================================
$('#formRegistrarCategoria').on('submit', function (e) {
    e.preventDefault();
    const $form = $(this);

    if ($form.find('.is-invalid').length) return;

    $.ajax({
        url: $form.attr('action'),
        type: 'POST',
        data: $form.serialize(),
        dataType: 'json',
        success: function (r) {
            $('#categoriaModal').modal('hide');
            $('#categoriaModal').one('hidden.bs.modal', function () {
                limpiarBackdrop();
                Swal.fire({
                    icon: r.success ? 'success' : 'error',
                    title: r.success ? 'Éxito' : 'Error',
                    text: r.mensaje,
                    timer: r.success ? 1500 : 2500,
                    showConfirmButton: !r.success
                });
                if (r.success) {
                    $form[0].reset();
                    $form.find('input').each((_, el) => clearValidation($(el)));
                    recargarTabla();
                }
            });
        },
        error: function () {
            $('#categoriaModal').modal('hide');
            $('#categoriaModal').one('hidden.bs.modal', function () {
                limpiarBackdrop();
                Swal.fire('Error', 'No se pudo registrar la categoría.', 'error');
            });
        }
    });
});

// ============================================================
// EDITAR CATEGORÍA
// ============================================================
$(document).on('submit', '.formEditarCategoria', function (e) {
    e.preventDefault();
    const $form = $(this);
    const modal = $form.closest('.modal');

    const $inputs = $form.find('input');
    const hayCambios = $inputs.toArray().some(input => {
        const $i = $(input);
        return $i.val() !== $i.data('original');
    });

    if (!hayCambios) {
        modal.modal('hide');
        modal.one('hidden.bs.modal', function () {
            limpiarBackdrop();
            Swal.fire({
                icon: 'info',
                title: 'Sin cambios',
                text: 'No se realizaron modificaciones.',
                timer: 2000,
                showConfirmButton: false
            });
        });
        return;
    }

    $.ajax({
        url: $form.attr('action'),
        type: 'POST',
        data: $form.serialize(),
        dataType: 'json',
        success: function (r) {
            modal.modal('hide');
            modal.one('hidden.bs.modal', function () {
                limpiarBackdrop();
                Swal.fire({
                    icon: r.success ? 'success' : 'error',
                    title: r.success ? 'Actualizado' : 'Error',
                    text: r.mensaje,
                    timer: r.success ? 1500 : 2500,
                    showConfirmButton: !r.success
                });
                if (r.success) recargarTabla();
            });
        },
        error: function () {
            modal.modal('hide');
            modal.one('hidden.bs.modal', function () {
                limpiarBackdrop();
                Swal.fire('Error', 'No se pudo actualizar la categoría.', 'error');
            });
        }
    });
});

// ============================================================
// ELIMINAR CATEGORÍA
// ============================================================
$('#formEliminarCategoria').on('submit', function (e) {
    e.preventDefault();
    const $form = $(this);

    $.ajax({
        url: $form.attr('action'),
        type: 'POST',
        data: $form.serialize(),
        dataType: 'json',
        success: function (r) {
            $('#delete-categoria-modal').modal('hide');
            $('#delete-categoria-modal').one('hidden.bs.modal', function () {
                limpiarBackdrop();
                Swal.fire({
                    icon: r.success ? 'success' : 'error',
                    title: r.success ? 'Eliminado' : 'Error',
                    text: r.mensaje,
                    timer: r.success ? 1500 : 2500,
                    showConfirmButton: !r.success
                });
                if (r.success) recargarTabla();
            });
        },
        error: function () {
            $('#delete-categoria-modal').modal('hide');
            $('#delete-categoria-modal').one('hidden.bs.modal', function () {
                limpiarBackdrop();
                Swal.fire('Error', 'No se pudo eliminar la categoría.', 'error');
            });
        }
    });
});

// ============================================================
// FUNCIONES GLOBALES
// ============================================================
function limpiarBackdrop() {
    $('.modal-backdrop').remove();
    $('body').removeClass('modal-open');
}

window.setDeleteId = id => $('#delete_categoria_id').val(id);
