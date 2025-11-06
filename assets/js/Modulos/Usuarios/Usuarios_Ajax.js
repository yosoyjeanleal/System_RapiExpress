$(document).ready(function() {

    // ============================================================
    // REPARAR PANTALLA NEGRA DESPUÉS DE CERRAR MODAL/SWEETALERT
    // ============================================================
    $(document).on('hidden.bs.modal', function() {
        setTimeout(() => {
            $('.swal2-container').remove();
            $('.modal-backdrop').remove();
            $('body').removeClass('modal-open');
            $('body').css('padding-right', '');
        }, 300);
    });

    // ============================================================
    // RECARGAR TABLA CON MODALES (Patrón Sucursales)
    // ============================================================
    function recargarTablaUsuarios() {
        $.ajax({
            url: 'index.php?c=usuario&a=index',
            type: 'GET',
            success: function(html) {
                const $html = $(html);

                // Destruir DataTable si existe
                if ($.fn.DataTable.isDataTable('#usuariosTable')) {
                    $('#usuariosTable').DataTable().destroy();
                }

                // Reemplazar tbody
                const nuevoTbody = $html.find('#usuariosTable tbody').html();
                $('#usuariosTable tbody').html(nuevoTbody);

                // Reemplazar modales de edición y visualización
                $('.modal.fade[id^="edit-usuario-modal"], .modal.fade[id^="view-usuario-modal"]').remove();
                const nuevosModales = $html.find('.modal.fade[id^="edit-usuario-modal"], .modal.fade[id^="view-usuario-modal"]');
                $('body').append(nuevosModales);

                // Reinicializar DataTable
                $('#usuariosTable').DataTable({
                    responsive: true,
                    autoWidth: false,
                    language: { url: 'assets/Temple/src/plugins/datatables/js/es_es.json' },
                    columnDefs: [{ targets: 'datatable-nosort', orderable: false }]
                });

                // Restaurar colores de íconos
                $('.table-actions a').each(function() {
                    const color = $(this).data('color');
                    if (color) $(this).find('i').css('color', color);
                });
            },
            error: function() {
                Swal.fire('Error', 'No se pudo recargar la lista de usuarios.', 'error');
            }
        });
    }

    // ============================================================
    // FUNCIONES AUXILIARES
    // ============================================================
    function mostrarEstado($input, estado, mensaje = '') {
        if (estado === 'ok') {
            $input.removeClass('is-invalid').addClass('is-valid');
            $input.next('.invalid-feedback').remove();
        } else {
            $input.removeClass('is-valid').addClass('is-invalid');
            if ($input.next('.invalid-feedback').length === 0) {
                $input.after(`<div class="invalid-feedback">${mensaje}</div>`);
            } else {
                $input.next('.invalid-feedback').text(mensaje);
            }
        }
    }

    // ============================================================
    // VALIDACIONES DINÁMICAS (Registrar y Editar)
    // ============================================================
    
    // Validar Cédula (6-23 dígitos)
    $(document).on('input', 'input[name="Cedula_Identidad"]', function() {
        const regex = /^\d{6,23}$/;
        const valor = $(this).val().trim();
        mostrarEstado($(this), regex.test(valor) ? 'ok' : 'error', 
            'La cédula debe contener entre 6 y 23 dígitos');
    });

    // Validar Username (4-20 caracteres, letras, números y guión bajo)
    $(document).on('input', 'input[name="Username"]', function() {
        const regex = /^[a-zA-Z0-9_]{4,20}$/;
        const valor = $(this).val().trim();
        mostrarEstado($(this), regex.test(valor) ? 'ok' : 'error', 
            'Usuario debe tener 4-20 caracteres (letras, números, _)');
    });

    // Validar Nombres (solo letras y espacios)
    $(document).on('input', 'input[name="Nombres_Usuario"]', function() {
        const regex = /^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/;
        const valor = $(this).val().trim();
        if (valor === '') {
            mostrarEstado($(this), 'error', 'Los nombres son obligatorios');
        } else {
            mostrarEstado($(this), regex.test(valor) ? 'ok' : 'error', 
                'Solo se permiten letras y espacios');
        }
    });

    // Validar Apellidos (solo letras y espacios)
    $(document).on('input', 'input[name="Apellidos_Usuario"]', function() {
        const regex = /^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/;
        const valor = $(this).val().trim();
        if (valor === '') {
            mostrarEstado($(this), 'error', 'Los apellidos son obligatorios');
        } else {
            mostrarEstado($(this), regex.test(valor) ? 'ok' : 'error', 
                'Solo se permiten letras y espacios');
        }
    });

    // Validar Email
    $(document).on('input', 'input[name="Correo_Usuario"]', function() {
        const regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        const valor = $(this).val().trim();
        mostrarEstado($(this), regex.test(valor) ? 'ok' : 'error', 
            'Formato de correo inválido');
    });

    // Validar Teléfono (7-15 dígitos)
    $(document).on('input', 'input[name="Telefono_Usuario"]', function() {
        const regex = /^\d{7,15}$/;
        const valor = $(this).val().trim();
        if (valor === '') {
            $(this).removeClass('is-invalid is-valid');
            $(this).next('.invalid-feedback').remove();
        } else {
            mostrarEstado($(this), regex.test(valor) ? 'ok' : 'error', 
                'El teléfono debe contener entre 7 y 15 dígitos');
        }
    });

    // Validar Dirección (opcional)
    $(document).on('input', 'input[name="Direccion_Usuario"]', function() {
        const regex = /^[a-zA-Z0-9\sáéíóúÁÉÍÓÚñÑ.,\-()_]{1,255}$/;
        const valor = $(this).val().trim();
        if (valor === '') {
            $(this).removeClass('is-invalid is-valid');
            $(this).next('.invalid-feedback').remove();
        } else {
            mostrarEstado($(this), regex.test(valor) ? 'ok' : 'error', 
                'Solo letras, números y caracteres (,.-()_) permitidos');
        }
    });

    // Validar Contraseña (mínimo 6 caracteres, al menos una letra y un número)
    $(document).on('input', 'input[name="Password"]', function() {
        const valor = $(this).val();
        const $container = $(this).closest('.form-group');
        
        // Validaciones individuales
        const minLength = valor.length >= 6;
        const hasLetter = /[A-Za-z]/.test(valor);
        const hasNumber = /\d/.test(valor);
        const isValid = minLength && hasLetter && hasNumber;

        if (valor === '') {
            $(this).removeClass('is-invalid is-valid');
            $(this).next('.invalid-feedback').remove();
        } else if (!isValid) {
            let mensaje = 'La contraseña debe tener: ';
            if (!minLength) mensaje += 'mínimo 6 caracteres, ';
            if (!hasLetter) mensaje += 'al menos una letra, ';
            if (!hasNumber) mensaje += 'al menos un número';
            mensaje = mensaje.replace(/, $/, '');
            
            mostrarEstado($(this), 'error', mensaje);
        } else {
            mostrarEstado($(this), 'ok', '');
        }
    });

    // Validar selects requeridos
    $(document).on('change', 'select[name="ID_Sucursal"], select[name="ID_Cargo"]', function() {
        const valor = $(this).val();
        const nombre = $(this).attr('name') === 'ID_Sucursal' ? 'Sucursal' : 'Cargo';
        mostrarEstado($(this), valor ? 'ok' : 'error', `Debe seleccionar una ${nombre}`);
    });

    // ============================================================
    // TOGGLE PASSWORD
    // ============================================================
    $(document).on('click', '.toggle-password', function() {
        const input = $(this).siblings('input');
        const icon = $(this).find('i');
        const isPassword = input.attr('type') === 'password';
        input.attr('type', isPassword ? 'text' : 'password');
        icon.toggleClass('fa-eye fa-eye-slash');
    });

    // ============================================================
    // REGISTRAR USUARIO
    // ============================================================
    $('#formRegistrarUsuario').on('submit', function(e) {
        e.preventDefault();
        const $form = $(this);
        const datos = $form.serialize();

        $.ajax({
            url: $form.attr('action'),
            type: 'POST',
            data: datos,
            dataType: 'json',
            success: function(respuesta) {
                $('#usuarioModal').modal('hide');

                setTimeout(() => {
                    if (respuesta.estado === 'success') {
                        $form[0].reset();
                        $form.find('.is-valid, .is-invalid').removeClass('is-valid is-invalid');
                        $form.find('.invalid-feedback').remove();
                        
                        Swal.fire({
                            icon: 'success',
                            title: 'Éxito',
                            text: respuesta.mensaje,
                            timer: 1500,
                            showConfirmButton: false
                        });
                        recargarTablaUsuarios();
                    } else {
                        Swal.fire('Error', respuesta.mensaje, 'error');
                    }
                }, 300);
            },
            error: function() {
                Swal.fire('Error', 'No se pudo registrar el usuario.', 'error');
            }
        });
    });

    // ============================================================
    // DETECTAR CAMBIOS EN MODAL DE EDITAR
    // ============================================================
    let datosOriginalesEdicion = {};

    // Guardar datos originales al abrir modal de edición
    $(document).on('show.bs.modal', '.modal[id^="edit-usuario-modal"]', function() {
        const $modal = $(this);
        const $form = $modal.find('form[id^="formEditarUsuario-"]');

        datosOriginalesEdicion = {};
        $form.find('input, select, textarea').each(function() {
            const name = $(this).attr('name');
            if (name) datosOriginalesEdicion[name] = $(this).val();
        });
    });

    // Verificar cambios antes de enviar
    $(document).on('submit', 'form[id^="formEditarUsuario-"]', function(e) {
        e.preventDefault();
        const $form = $(this);

        let hayCambios = false;
        $form.find('input, select, textarea').each(function() {
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

        const datos = $form.serialize();
        $.ajax({
            url: $form.attr('action'),
            type: 'POST',
            data: datos,
            dataType: 'json',
            success: function(respuesta) {
                $form.closest('.modal').modal('hide');

                setTimeout(() => {
                    if (respuesta.estado === 'success') {
                        Swal.fire({
                            icon: 'success',
                            title: 'Actualizado',
                            text: respuesta.mensaje,
                            timer: 1500,
                            showConfirmButton: false
                        });
                        recargarTablaUsuarios();
                    } else {
                        Swal.fire('Error', respuesta.mensaje, 'error');
                    }
                }, 300);
            },
            error: function() {
                Swal.fire('Error', 'No se pudo actualizar el usuario.', 'error');
            }
        });
    });

    // Limpiar datos al cerrar modal de edición
    $(document).on('hidden.bs.modal', '.modal[id^="edit-usuario-modal"]', function() {
        datosOriginalesEdicion = {};
        $(this).find('.is-valid, .is-invalid').removeClass('is-valid is-invalid');
        $(this).find('.invalid-feedback').remove();
    });

    // ============================================================
    // ELIMINAR USUARIO
    // ============================================================
    $('#formEliminarUsuario').on('submit', function(e) {
        e.preventDefault();
        const $form = $(this);
        const datos = $form.serialize();

        $.ajax({
            url: $form.attr('action'),
            type: 'POST',
            data: datos,
            dataType: 'json',
            success: function(respuesta) {
                $('#delete-usuario-modal').modal('hide');

                setTimeout(() => {
                    if (respuesta.estado === 'success') {
                        Swal.fire({
                            icon: 'success',
                            title: 'Eliminado',
                            text: respuesta.mensaje,
                            timer: 1500,
                            showConfirmButton: false
                        });
                        recargarTablaUsuarios();
                    } else {
                        Swal.fire('Error', respuesta.mensaje, 'error');
                    }
                }, 300);
            },
            error: function() {
                Swal.fire('Error', 'No se pudo eliminar el usuario.', 'error');
            }
        });
    });

    // ============================================================
    // LIMPIAR VALIDACIONES AL CERRAR MODAL DE REGISTRO
    // ============================================================
    $(document).on('hidden.bs.modal', '#usuarioModal', function() {
        const $form = $('#formRegistrarUsuario');
        $form[0].reset();
        $form.find('.is-valid, .is-invalid').removeClass('is-valid is-invalid');
        $form.find('.invalid-feedback').remove();
    });

});

// ============================================================
// FUNCIÓN GLOBAL PARA SET DELETE ID
// ============================================================
window.setDeleteUsuarioId = function(id) {
    $('#delete_usuario_id').val(id);
};