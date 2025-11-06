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
    // RECARGAR TABLA CON MODALES
    // ============================================================
    function recargarTablaPaquetes() {
        $.ajax({
            url: 'index.php?c=paquete&a=index',
            type: 'GET',
            success: function(html) {
                const $html = $(html);
                
                // Destruir DataTable existente antes de actualizar
                if ($.fn.DataTable.isDataTable('#paquetesTable')) {
                    $('#paquetesTable').DataTable().destroy();
                }

                // Reemplazar tbody completo (incluye los data-attributes actualizados)
                const nuevoTbody = $html.find('#paquetesTable tbody').html();
                $('#paquetesTable tbody').html(nuevoTbody);

                // Reemplazar modales de edición
                const nuevosModales = $html.find('.modal.fade[id^="edit-paquete-"]');
                $('.modal.fade[id^="edit-paquete-"]').remove();
                $('body').append(nuevosModales);

                // Reinicializar DataTable
                $('#paquetesTable').DataTable({
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

                // ✅ Desmarcar todos los checkboxes después de recargar
                $('.paquete-check').prop('checked', false);
                
                // ✅ Deshabilitar botón de imprimir
                const btnImprimir = document.getElementById("btnImprimirSeleccionado");
                if (btnImprimir) {
                    btnImprimir.disabled = true;
                }

                // Reinicializar checkboxes con los nuevos data-attributes
                inicializarCheckboxes();
            },
            error: function() {
                Swal.fire('Error', 'No se pudo recargar la lista de paquetes.', 'error');
            }
        });
    }

    // ============================================================
    // VALIDACIONES DINÁMICAS
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

    $(document).on('input', 'input[name="Nombre_Instrumento"]', function() {
        const regex = /^[a-zA-Z0-9\sáéíóúÁÉÍÓÚñÑ.,\-()_]+$/;
        const valor = $(this).val().trim();
        if (valor === '') {
            mostrarEstado($(this), 'ok', '');
        } else {
            mostrarEstado($(this), regex.test(valor) ? 'ok' : 'error', 
                'Solo letras, números y caracteres válidos (,.-())');
        }
    });

    $(document).on('input', 'input[name="Paquete_Peso"]', function() {
        const valor = parseFloat($(this).val());
        mostrarEstado($(this), valor > 0 ? 'ok' : 'error', 'El peso debe ser mayor a 0');
    });

    $(document).on('input', 'input[name="Paquete_Piezas"]', function() {
        const valor = parseInt($(this).val());
        mostrarEstado($(this), valor > 0 ? 'ok' : 'error', 'Las piezas deben ser mayor a 0');
    });

    $(document).on('change', 'select[name="ID_Cliente"]', function() {
        mostrarEstado($(this), $(this).val() ? 'ok' : 'error', 'Debe seleccionar un cliente');
    });

    $(document).on('change', 'select[name="ID_Categoria"]', function() {
        mostrarEstado($(this), $(this).val() ? 'ok' : 'error', 'Debe seleccionar una categoría');
    });

    $(document).on('change', 'select[name="ID_Courier"]', function() {
        mostrarEstado($(this), $(this).val() ? 'ok' : 'error', 'Debe seleccionar un courier');
    });

    $(document).on('change', 'select[name="ID_Sucursal"]', function() {
        mostrarEstado($(this), $(this).val() ? 'ok' : 'error', 'Debe seleccionar una sucursal');
    });

    // ============================================================
    // REGISTRAR PAQUETE
    // ============================================================
    $('#formRegistrarPaquete').on('submit', function(e) {
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
            success: function(res) {
                $('#paqueteModal').modal('hide');

                setTimeout(() => {
                    if (res.success) {
                        $form[0].reset();
                        $form.find('.is-valid, .is-invalid').removeClass('is-valid is-invalid');
                        $form.find('.invalid-feedback').remove();
                        Swal.fire({
                            icon: 'success',
                            title: 'Éxito',
                            text: res.message,
                            timer: 1500,
                            showConfirmButton: false
                        });
                        recargarTablaPaquetes();
                    } else {
                        Swal.fire({ icon: 'error', title: 'Error', text: res.message });
                    }
                }, 300);
            },
            error: function(xhr) {
                let mensaje = 'No se pudo registrar el paquete.';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    mensaje = xhr.responseJSON.message;
                }
                Swal.fire({ icon: 'error', title: 'Error', text: mensaje });
            }
        });
    });

    // ============================================================
    // DETECTAR CAMBIOS EN MODAL DE EDITAR
    // ============================================================
    let datosOriginalesEdicion = {};

    // Guardar datos originales al abrir modal de edición
    $(document).on('show.bs.modal', '.modal[id^="edit-paquete-"]', function() {
        const $modal = $(this);
        const $form = $modal.find('form[id^="formEditarPaquete-"]');

        datosOriginalesEdicion = {};
        $form.find('input, select, textarea').each(function() {
            const name = $(this).attr('name');
            if (name) datosOriginalesEdicion[name] = $(this).val();
        });
    });

    // Verificar cambios antes de enviar
    $(document).on('submit', 'form[id^="formEditarPaquete-"]', function(e) {
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

        const datos = new FormData(this);
        $.ajax({
            url: $form.attr('action'),
            type: 'POST',
            data: datos,
            contentType: false,
            processData: false,
            dataType: 'json',
            success: function(res) {
                $form.closest('.modal').modal('hide');

                setTimeout(() => {
                    if (res.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Actualizado',
                            text: res.message,
                            timer: 1500,
                            showConfirmButton: false
                        });
                        recargarTablaPaquetes();
                    } else {
                        Swal.fire({ icon: 'error', title: 'Error', text: res.message });
                    }
                }, 300);
            },
            error: function(xhr) {
                let mensaje = 'No se pudo actualizar el paquete.';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    mensaje = xhr.responseJSON.message;
                }
                Swal.fire({ icon: 'error', title: 'Error', text: mensaje });
            }
        });
    });

    // Limpiar datos al cerrar modal
    $(document).on('hidden.bs.modal', '.modal[id^="edit-paquete-"]', function() {
        datosOriginalesEdicion = {};
    });

    // ============================================================
    // ELIMINAR PAQUETE
    // ============================================================
    $('#formEliminarPaquete').on('submit', function(e) {
        e.preventDefault();
        const $form = $(this);

        $.ajax({
            url: $form.attr('action'),
            type: 'POST',
            data: $form.serialize(),
            dataType: 'json',
            success: function(res) {
                $('#delete-paquete-modal').modal('hide');

                setTimeout(() => {
                    if (res.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Eliminado',
                            text: res.message,
                            timer: 1500,
                            showConfirmButton: false
                        });
                        recargarTablaPaquetes();
                    } else {
                        Swal.fire({ icon: 'error', title: 'Error', text: res.message });
                    }
                }, 300);
            },
            error: function(xhr) {
                let mensaje = 'No se puede eliminar el paquete porque está asociado a registros relacionados.';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    mensaje = xhr.responseJSON.message;
                }
                Swal.fire({ icon: 'error', title: 'Error', text: mensaje });
            }
        });
    });

    // ============================================================
    // CHECKBOXES E IMPRESIÓN
    // ============================================================
    function inicializarCheckboxes() {
        const checkboxes = document.querySelectorAll(".paquete-check");
        const btnImprimir = document.getElementById("btnImprimirSeleccionado");

        // Limpiar listeners previos
        checkboxes.forEach(chk => {
            // Clonar el elemento para eliminar todos los event listeners
            const newChk = chk.cloneNode(true);
            chk.parentNode.replaceChild(newChk, chk);
        });

        // Agregar nuevos listeners a los checkboxes actualizados
        document.querySelectorAll(".paquete-check").forEach(chk => {
            chk.addEventListener("change", function() {
                const selected = document.querySelectorAll(".paquete-check:checked");
                if (btnImprimir) {
                    btnImprimir.disabled = (selected.length !== 1);
                }
            });
        });

        // Actualizar estado del botón
        if (btnImprimir) {
            const selected = document.querySelectorAll(".paquete-check:checked");
            btnImprimir.disabled = (selected.length !== 1);
        }
    }

    function abrirModalImprimir() {
        const selected = document.querySelector(".paquete-check:checked");
        if (!selected) {
            Swal.fire({
                icon: 'warning',
                title: 'Atención',
                text: 'Debe seleccionar un paquete.',
                timer: 2000,
                showConfirmButton: false
            });
            return;
        }

        // ✅ Limpiar el modal primero para forzar actualización visual
        $('#imprimirPaqueteModal').modal('hide');
        
        // ✅ Pequeño delay para asegurar limpieza del modal
        setTimeout(() => {
            // Leer los datos ACTUALIZADOS del checkbox
            document.getElementById("detalleTracking").innerText = selected.dataset.tracking || '-';
            document.getElementById("detalleCliente").innerText = selected.dataset.cliente || '-';
            document.getElementById("detalleInstrumento").innerText = selected.dataset.instrumento || '-';
            document.getElementById("detalleCategoria").innerText = selected.dataset.categoria || '-';
            document.getElementById("detalleSucursal").innerText = selected.dataset.sucursal || '-';
            document.getElementById("detalleCourier").innerText = selected.dataset.courier || '-';
            document.getElementById("detalleDescripcion").innerText = selected.dataset.descripcion || '-';
            document.getElementById("detallePeso").innerText = selected.dataset.peso || '-';
            
            // ✅ También mostrar las piezas si existe el elemento
            const detallePiezasElement = document.getElementById("detallePiezas");
            if (detallePiezasElement) {
                detallePiezasElement.innerText = selected.dataset.piezas || '1';
            }
            
            // ✅ Actualizar el QR con los datos más recientes y timestamp para evitar caché
            const qrCode = selected.dataset.qr;
            const qrContainer = document.getElementById("detalleQR");
            const timestamp = new Date().getTime();
            
            if (qrCode && qrCode !== '' && qrCode !== 'undefined' && qrCode !== 'null') {
                qrContainer.innerHTML = `<img src="src/storage/qr/${qrCode}?v=${timestamp}" width="120" alt="Código QR" onerror="this.parentElement.innerHTML='<p class=\\'text-muted\\'>Error al cargar QR</p>'">`;
            } else {
                qrContainer.innerHTML = '<p class="text-muted">No hay código QR disponible</p>';
            }
            
            // ✅ Mostrar el modal actualizado
            $('#imprimirPaqueteModal').modal('show');
        }, 200);
    }

    // Vincular el botón de imprimir con la función usando delegación de eventos
    $(document).on('click', '#btnImprimirSeleccionado', abrirModalImprimir);

    // ✅ Limpiar modal de impresión cuando se cierre para forzar recarga
    $(document).on('hidden.bs.modal', '#imprimirPaqueteModal', function() {
        // Limpiar todos los campos
        document.getElementById("detalleTracking").innerText = '-';
        document.getElementById("detalleCliente").innerText = '-';
        document.getElementById("detalleInstrumento").innerText = '-';
        document.getElementById("detalleCategoria").innerText = '-';
        document.getElementById("detalleSucursal").innerText = '-';
        document.getElementById("detalleCourier").innerText = '-';
        document.getElementById("detalleDescripcion").innerText = '-';
        document.getElementById("detallePeso").innerText = '-';
        document.getElementById("detalleQR").innerHTML = '';
        
        const detallePiezasElement = document.getElementById("detallePiezas");
        if (detallePiezasElement) {
            detallePiezasElement.innerText = '-';
        }
    });

    // ✅ Limpiar iframe de etiqueta cuando se cierre el modal
    $(document).on('hidden.bs.modal', '#modalEtiqueta', function() {
        document.getElementById("etiquetaFrame").src = 'about:blank';
    });

    // Inicializar checkboxes al cargar
    inicializarCheckboxes();

});

// ============================================================
// FUNCIÓN SET DELETE ID (Global)
// ============================================================
window.setDeletePaqueteId = function(id) {
    document.getElementById('delete_paquete_id').value = id;
};

// ============================================================
// FUNCIÓN IMPRIMIR PAQUETE (Global)
// ============================================================
window.imprimirPaquete = function() {
    const tracking = document.getElementById("detalleTracking").innerText;
    const cliente = document.getElementById("detalleCliente").innerText;
    const instrumento = document.getElementById("detalleInstrumento").innerText;
    const categoria = document.getElementById("detalleCategoria").innerText;
    const sucursal = document.getElementById("detalleSucursal").innerText;
    const courier = document.getElementById("detalleCourier").innerText;
    const descripcion = document.getElementById("detalleDescripcion").innerText;
    const peso = document.getElementById("detallePeso").innerText;

    const selected = document.querySelector(".paquete-check:checked");
    const qrFile = selected ? selected.dataset.qr : '';

    // ✅ Agregar timestamp para evitar caché y forzar recarga del QR
    const timestamp = new Date().getTime();
    const url = `src/views/partels/etiquetas.php?tracking=${encodeURIComponent(tracking)}&cliente=${encodeURIComponent(cliente)}&instrumento=${encodeURIComponent(instrumento)}&categoria=${encodeURIComponent(categoria)}&sucursal=${encodeURIComponent(sucursal)}&courier=${encodeURIComponent(courier)}&descripcion=${encodeURIComponent(descripcion)}&peso=${encodeURIComponent(peso)}&qr=${encodeURIComponent(qrFile)}&t=${timestamp}`;

    // ✅ Limpiar el iframe antes de cargar nuevo contenido
    const iframe = document.getElementById("etiquetaFrame");
    iframe.src = 'about:blank';
    
    // ✅ Pequeño delay para asegurar que se limpió
    setTimeout(() => {
        iframe.src = url;
    }, 100);

    $('#modalEtiqueta').modal('show');
    $('#imprimirPaqueteModal').modal('hide');
};

// ============================================================
// FUNCIÓN IMPRIMIR ETIQUETA (Global)
// ============================================================
window.imprimirEtiqueta = function() {
    const iframe = document.getElementById('etiquetaFrame');
    iframe.contentWindow.print();
};