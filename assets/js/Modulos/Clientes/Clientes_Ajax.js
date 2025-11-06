$(document).ready(function() {

    // ============================================================
    // RECARGAR TABLA DINÁMICAMENTE
    // ============================================================
    function recargarTablaClientes() {
        if ($.fn.DataTable.isDataTable('#clientesTable')) {
            $('#clientesTable').DataTable().destroy();
        }

        $.ajax({
            url: 'index.php?c=cliente&a=index',
            type: 'GET',
            success: function (html) {
                const nuevoTbody = $(html).find('#clientesTable tbody').html();
                const nuevosModales = $(html).find('.modal.fade[id^="edit-cliente-modal"]');

                $('#clientesTable tbody').html(nuevoTbody);
                $('.modal.fade[id^="edit-cliente-modal"]').remove();
                $('body').append(nuevosModales);

                $('#clientesTable').DataTable({
                    responsive: true,
                    autoWidth: false,
                    language: { url: 'assets/Temple/src/plugins/datatables/js/es_es.json' },
                    columnDefs: [{ targets: 'datatable-nosort', orderable: false }]
                });

                // Restaurar colores de íconos
                aplicarColoresIconos();
            },
            error: function () {
                Swal.fire('Error', 'No se pudo recargar la lista de clientes.', 'error');
            }
        });
    }

    // ===============================
    // REGISTRAR CLIENTE
    // ===============================
    $('#formRegistrarCliente').on('submit', function(e) {
        e.preventDefault();
        const $form = $(this);

        if ($form.find('.is-invalid').length) return;

        const datos = new FormData(this);

        $.ajax({
            url: $form.attr('action'),
            type: 'POST',
            data: datos,
            contentType: false,
            processData: false,
            dataType: 'json',
            success: function(res) {
                $('#clienteModal').modal('hide');
                $('#clienteModal').one('hidden.bs.modal', function() {
                    Swal.fire({
                        icon: res.estado === 'success' ? 'success' : 'error',
                        title: res.estado === 'success' ? 'Éxito' : 'Error',
                        text: res.mensaje,
                        timer: res.estado === 'success' ? 1500 : 2500,
                        showConfirmButton: res.estado !== 'success'
                    });
                    if (res.estado === 'success') {
                        limpiarFormulario($form);
                        recargarTablaClientes();
                    }
                });
            },
            error: function() {
                $('#clienteModal').modal('hide');
                $('#clienteModal').one('hidden.bs.modal', function() {
                    Swal.fire('Error', 'No se pudo registrar el cliente.', 'error');
                });
            }
        });
    });

    // ============================================================
    // DETECTAR CAMBIOS EN MODAL DE EDITAR
    // ============================================================
    let datosOriginalesEdicion = {};

    $(document).on('show.bs.modal', '.modal[id^="edit-cliente-modal"]', function () {
        const $modal = $(this);
        const $form = $modal.find('form[id^="formEditarCliente-"]');

        datosOriginalesEdicion = {};
        $form.find('input, select, textarea').each(function () {
            const name = $(this).attr('name');
            if (name) datosOriginalesEdicion[name] = $(this).val();
        });

        // Aplicar validaciones al modal
        aplicarValidaciones($form);
    });

    // ===============================
    // EDITAR CLIENTE
    // ===============================
    $(document).on('submit', 'form[id^="formEditarCliente-"]', function(e) {
        e.preventDefault();
        const $form = $(this);
        const modal = $form.closest('.modal');

        // Verificar cambios
        let hayCambios = false;
        $form.find('input, select, textarea').each(function () {
            const name = $(this).attr('name');
            if (name && datosOriginalesEdicion[name] !== $(this).val()) {
                hayCambios = true;
                return false;
            }
        });

        if (!hayCambios) {
            modal.modal('hide');
            modal.one('hidden.bs.modal', function() {
                Swal.fire({
                    icon: 'info',
                    title: 'Sin cambios',
                    text: 'No se detectaron modificaciones en los datos.',
                    timer: 2000,
                    showConfirmButton: false
                });
            });
            return;
        }

        // Continuar con la actualización
        const datos = new FormData(this);
        $.ajax({
            url: $form.attr('action'),
            type: 'POST',
            data: datos,
            contentType: false,
            processData: false,
            dataType: 'json',
            success: function(res) {
                modal.modal('hide');
                modal.one('hidden.bs.modal', function() {
                    Swal.fire({
                        icon: res.estado === 'success' ? 'success' : 'error',
                        title: res.estado === 'success' ? 'Actualizado' : 'Error',
                        text: res.mensaje,
                        timer: res.estado === 'success' ? 1500 : 2500,
                        showConfirmButton: res.estado !== 'success'
                    });
                    if (res.estado === 'success') recargarTablaClientes();
                });
            },
            error: function() {
                modal.modal('hide');
                modal.one('hidden.bs.modal', function() {
                    Swal.fire('Error', 'No se pudo actualizar el cliente.', 'error');
                });
            }
        });
    });

    // Limpiar datos al cerrar modal
    $(document).on('hidden.bs.modal', '.modal[id^="edit-cliente-modal"]', function () {
        datosOriginalesEdicion = {};
    });

    // ===============================
    // ELIMINAR CLIENTE
    // ===============================
    $('#formEliminarCliente').on('submit', function(e) {
        e.preventDefault();
        const $form = $(this);

        $.ajax({
            url: $form.attr('action'),
            type: 'POST',
            data: $form.serialize(),
            dataType: 'json',
            success: function(res) {
                $('#delete-cliente-modal').modal('hide');
                $('#delete-cliente-modal').one('hidden.bs.modal', function() {
                    Swal.fire({
                        icon: res.estado === 'success' ? 'success' : 'error',
                        title: res.estado === 'success' ? 'Eliminado' : 'Error',
                        text: res.mensaje,
                        timer: res.estado === 'success' ? 1500 : 2500,
                        showConfirmButton: res.estado !== 'success'
                    });
                    if (res.estado === 'success') recargarTablaClientes();
                });
            },
            error: function() {
                $('#delete-cliente-modal').modal('hide');
                $('#delete-cliente-modal').one('hidden.bs.modal', function() {
                    Swal.fire('Error', 'No se pudo eliminar el cliente.', 'error');
                });
            }
        });
    });

});