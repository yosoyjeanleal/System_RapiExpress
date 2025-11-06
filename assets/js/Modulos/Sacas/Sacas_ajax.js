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
    function recargarTablaSacas() {
        $.ajax({
            url: 'index.php?c=saca&a=index',
            type: 'GET',
            success: function(html) {
                const $html = $(html);
                
                // Destruir DataTable existente
                if ($.fn.DataTable.isDataTable('#tablaSacas')) {
                    $('#tablaSacas').DataTable().destroy();
                }

                // Reemplazar tbody
                const nuevoTbody = $html.find('#tablaSacas tbody').html();
                $('#tablaSacas tbody').html(nuevoTbody);

                // Reemplazar modales de edición
                const nuevosModales = $html.find('.modal.fade[id^="edit-saca-modal-"]');
                $('.modal.fade[id^="edit-saca-modal-"]').remove();
                $('body').append(nuevosModales);

                // Reinicializar DataTable
                $('#tablaSacas').DataTable({
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

                // Desmarcar radios
                $('input[name="selectSaca"]').prop('checked', false);
                $('#btnDetalle').prop('disabled', true);
                
                // Deshabilitar botón de imprimir
                const btnImprimir = document.getElementById("btnImprimirSaca");
                if (btnImprimir) {
                    btnImprimir.disabled = true;
                }

                // Reinicializar radios
                inicializarRadiosSaca();
            },
            error: function() {
                Swal.fire('Error', 'No se pudo recargar la lista de sacas.', 'error');
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

    $(document).on('input', 'input[name="Peso_Total"]', function() {
        const valor = parseFloat($(this).val());
        if ($(this).val().trim() === '') {
            mostrarEstado($(this), 'ok', '');
        } else {
            mostrarEstado($(this), valor >= 0 ? 'ok' : 'error', 'El peso debe ser mayor o igual a 0');
        }
    });

    $(document).on('change', 'select[name="ID_Usuario"]', function() {
        mostrarEstado($(this), $(this).val() ? 'ok' : 'error', 'Debe seleccionar un usuario');
    });

    $(document).on('change', 'select[name="ID_Sucursal"]', function() {
        mostrarEstado($(this), $(this).val() ? 'ok' : 'error', 'Debe seleccionar una sucursal');
    });

    // ============================================================
    // REGISTRAR SACA
    // ============================================================
    $('#formRegistrarSaca, form[action*="saca&a=registrar"]').on('submit', function(e) {
        e.preventDefault();
        const $form = $(this);
        const datos = new FormData(this);

        $.ajax({
            url: 'index.php?c=saca&a=registrar',
            type: 'POST',
            data: datos,
            contentType: false,
            processData: false,
            dataType: 'json',
            success: function(res) {
                $('#sacaModal').modal('hide');

                setTimeout(() => {
                    if (res.success) {
                        $form[0].reset();
                        $form.find('.is-valid, .is-invalid').removeClass('is-valid is-invalid');
                        $form.find('.invalid-feedback').remove();
                        Swal.fire({
                            icon: 'success',
                            title: 'Éxito',
                            text: res.message,
                            timer: 2000,
                            showConfirmButton: false
                        });
                        recargarTablaSacas();
                    } else {
                        Swal.fire({ icon: 'error', title: 'Error', text: res.message });
                    }
                }, 300);
            },
            error: function(xhr) {
                let mensaje = 'No se pudo registrar la saca.';
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
    let datosOriginalesEdicionSaca = {};

    $(document).on('show.bs.modal', '.modal[id^="edit-saca-modal-"]', function() {
        const $modal = $(this);
        const $form = $modal.find('form');

        datosOriginalesEdicionSaca = {};
        $form.find('input, select, textarea').each(function() {
            const name = $(this).attr('name');
            if (name) datosOriginalesEdicionSaca[name] = $(this).val();
        });
    });

    $(document).on('submit', 'form[action*="saca&a=editar"]', function(e) {
        e.preventDefault();
        const $form = $(this);

        let hayCambios = false;
        $form.find('input, select, textarea').each(function() {
            const name = $(this).attr('name');
            if (name && datosOriginalesEdicionSaca[name] !== $(this).val()) {
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
            url: 'index.php?c=saca&a=editar',
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
                        recargarTablaSacas();
                    } else {
                        Swal.fire({ icon: 'error', title: 'Error', text: res.message });
                    }
                }, 300);
            },
            error: function(xhr) {
                let mensaje = 'No se pudo actualizar la saca.';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    mensaje = xhr.responseJSON.message;
                }
                Swal.fire({ icon: 'error', title: 'Error', text: mensaje });
            }
        });
    });

    $(document).on('hidden.bs.modal', '.modal[id^="edit-saca-modal-"]', function() {
        datosOriginalesEdicionSaca = {};
    });

    // ============================================================
    // ELIMINAR SACA CON AJAX
    // ============================================================
    $(document).on('submit', 'form[action*="saca&a=eliminar"]', function(e) {
        e.preventDefault();
        const $form = $(this);
        
        Swal.fire({
            title: '¿Está seguro?',
            text: "¿Desea eliminar esta saca?",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: 'index.php?c=saca&a=eliminar',
                    type: 'POST',
                    data: $form.serialize(),
                    dataType: 'json',
                    success: function(res) {
                        if (res.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Eliminado',
                                text: res.message,
                                timer: 1500,
                                showConfirmButton: false
                            });
                            recargarTablaSacas();
                        } else {
                            Swal.fire({ icon: 'error', title: 'Error', text: res.message });
                        }
                    },
                    error: function(xhr) {
                        let mensaje = 'No se puede eliminar la saca porque está relacionada con otros registros.';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            mensaje = xhr.responseJSON.message;
                        }
                        Swal.fire({ icon: 'error', title: 'Error', text: mensaje });
                    }
                });
            }
        });
    });

    // ============================================================
    // RADIOS E IMPRESIÓN
    // ============================================================
    function inicializarRadiosSaca() {
        const radios = document.querySelectorAll('input[name="selectSaca"]');
        const btnImprimir = document.getElementById("btnImprimirSaca");
        const btnDetalle = document.getElementById("btnDetalle");

        radios.forEach(radio => {
            const newRadio = radio.cloneNode(true);
            radio.parentNode.replaceChild(newRadio, radio);
        });

        document.querySelectorAll('input[name="selectSaca"]').forEach(radio => {
            radio.addEventListener("change", function() {
                const selected = document.querySelector('input[name="selectSaca"]:checked');
                if (btnImprimir) {
                    btnImprimir.disabled = !selected;
                }
                if (btnDetalle) {
                    btnDetalle.disabled = !selected;
                }
            });
        });

        if (btnImprimir) {
            const selected = document.querySelector('input[name="selectSaca"]:checked');
            btnImprimir.disabled = !selected;
        }
        if (btnDetalle) {
            const selected = document.querySelector('input[name="selectSaca"]:checked');
            btnDetalle.disabled = !selected;
        }
    }

    function abrirModalImprimirSaca() {
        const selected = document.querySelector('input[name="selectSaca"]:checked');
        if (!selected) {
            Swal.fire({
                icon: 'warning',
                title: 'Atención',
                text: 'Debe seleccionar una saca.',
                timer: 2000,
                showConfirmButton: false
            });
            return;
        }

        const idSaca = selected.value;
        console.log('🔍 ID Saca seleccionada:', idSaca);
        
        // Mostrar loading
        Swal.fire({
            title: 'Cargando...',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        // Obtener datos de la saca por AJAX
        $.ajax({
            url: `index.php?c=saca&a=obtenerDatosImpresion&id=${idSaca}`,
            type: 'GET',
            dataType: 'json',
            success: function(res) {
                console.log('✅ Respuesta del servidor:', res);
                Swal.close();
                
                if (res.success) {
                    const data = res.data;
                    console.log('📦 Datos de la saca:', data);
                    
                    document.getElementById("detalleSacaCodigo").innerText = data.Codigo_Saca || '-';
                    document.getElementById("detalleSacaUsuario").innerText = data.Usuario || '-';
                    document.getElementById("detalleSacaSucursal").innerText = data.Sucursal || '-';
                    document.getElementById("detalleSacaEstado").innerText = data.Estado || '-';
                    document.getElementById("detalleSacaPeso").innerText = data.Peso_Total || '-';
                    document.getElementById("detalleSacaCantidad").innerText = data.Cantidad_Paquetes || '0';
                    document.getElementById("detalleSacaFecha").innerText = data.Fecha_Creacion || '-';
                    
                    // Actualizar QR con timestamp
                    const qrContainer = document.getElementById("detalleSacaQR");
                    const timestamp = new Date().getTime();
                    
                    // Siempre usar la generación dinámica para asegurar que se crea el QR
                    const qrUrl = `index.php?c=saca&a=generarQR&id=${idSaca}&t=${timestamp}`;
                    console.log('🔗 URL del QR:', qrUrl);
                    
                    qrContainer.innerHTML = `<img src="${qrUrl}" width="120" alt="Código QR" onerror="console.error('❌ Error al cargar QR'); this.parentElement.innerHTML='<p class=\\'text-muted\\'>Error al cargar QR</p>'" onload="console.log('✅ QR cargado correctamente')">`;
                    
                    $('#imprimirSacaModal').modal('show');
                } else {
                    console.error('❌ Error en la respuesta:', res.message);
                    Swal.fire({ icon: 'error', title: 'Error', text: res.message });
                }
            },
            error: function(xhr, status, error) {
                console.error('❌ Error AJAX:', {xhr, status, error});
                console.error('📄 Respuesta completa:', xhr.responseText);
                Swal.close();
                Swal.fire({ icon: 'error', title: 'Error', text: 'No se pudieron cargar los datos de la saca.' });
            }
        });
    }

    $(document).on('click', '#btnImprimirSaca', abrirModalImprimirSaca);

    // Limpiar modal al cerrar
    $(document).on('hidden.bs.modal', '#imprimirSacaModal', function() {
        document.getElementById("detalleSacaCodigo").innerText = '-';
        document.getElementById("detalleSacaUsuario").innerText = '-';
        document.getElementById("detalleSacaSucursal").innerText = '-';
        document.getElementById("detalleSacaEstado").innerText = '-';
        document.getElementById("detalleSacaPeso").innerText = '-';
        document.getElementById("detalleSacaCantidad").innerText = '-';
        document.getElementById("detalleSacaFecha").innerText = '-';
        document.getElementById("detalleSacaQR").innerHTML = '';
    });

    $(document).on('hidden.bs.modal', '#modalEtiquetaSaca', function() {
        document.getElementById("etiquetaSacaFrame").src = 'about:blank';
    });

    inicializarRadiosSaca();

});
// ============================================================
// FUNCIÓN GLOBAL PARA IMPRIMIR SACA (reemplazar en saca_ajax.js)
// ============================================================
window.imprimirSaca = function() {
    const codigo = document.getElementById("detalleSacaCodigo").innerText;
    const usuario = document.getElementById("detalleSacaUsuario").innerText;
    const sucursal = document.getElementById("detalleSacaSucursal").innerText;
    const estado = document.getElementById("detalleSacaEstado").innerText;
    const peso = document.getElementById("detalleSacaPeso").innerText;
    const cantidad = document.getElementById("detalleSacaCantidad").innerText;
    const fecha = document.getElementById("detalleSacaFecha").innerText;

    const selected = document.querySelector('input[name="selectSaca"]:checked');
    if (!selected) {
        Swal.fire({
            icon: 'warning',
            title: 'Atención',
            text: 'No se ha seleccionado ninguna saca.',
            timer: 2000
        });
        return;
    }
    
    const idSaca = selected.value;

    console.log('🖨️ Imprimiendo saca:', {codigo, usuario, sucursal, estado, peso, cantidad, fecha, idSaca});

    const timestamp = new Date().getTime();
    
    // ✅ CONSTRUIR URL CORRECTA (sin 'src/views/partels/')
    const params = new URLSearchParams({
        codigo: codigo,
        usuario: usuario,
        sucursal: sucursal,
        estado: estado,
        peso: peso,
        cantidad: cantidad,
        fecha: fecha,
        id: idSaca,
        t: timestamp
    });
    
    // ✅ Ruta corregida: debe estar en src/views/partels/etiqueta_saca.php
    const url = `src/views/partels/etiqueta_saca.php?${params.toString()}`;

    console.log('🔗 URL de etiqueta:', url);

    const iframe = document.getElementById("etiquetaSacaFrame");
    
    // Limpiar iframe antes de cargar
    iframe.src = 'about:blank';
    
    // Pequeño delay para asegurar que se limpia correctamente
    setTimeout(() => {
        iframe.src = url;
        console.log('✅ Iframe cargado con URL:', url);
        
        // Esperar a que el iframe cargue antes de abrir el modal
        iframe.onload = function() {
            console.log('✅ Contenido del iframe cargado correctamente');
            $('#modalEtiquetaSaca').modal('show');
            $('#imprimirSacaModal').modal('hide');
        };
        
        // Manejar errores de carga
        iframe.onerror = function() {
            console.error('❌ Error al cargar el iframe');
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'No se pudo cargar la etiqueta. Verifica la ruta del archivo.'
            });
        };
    }, 100);
};

window.imprimirEtiquetaSaca = function() {
    console.log('🖨️ Ejecutando impresión desde iframe...');
    const iframe = document.getElementById('etiquetaSacaFrame');
    
    // Verificar que el iframe esté cargado
    if (iframe.contentWindow) {
        try {
            iframe.contentWindow.print();
        } catch (e) {
            console.error('❌ Error al imprimir:', e);
            Swal.fire({
                icon: 'error', 
                title: 'Error', 
                text: 'No se pudo acceder al contenido para imprimir. Intenta de nuevo.'
            });
        }
    } else {
        console.error('❌ No se pudo acceder al iframe');
        Swal.fire({
            icon: 'error', 
            title: 'Error', 
            text: 'No se pudo cargar la etiqueta para imprimir'
        });
    }
};