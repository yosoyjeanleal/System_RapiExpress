/**
 * ============================================================
 * AJAX UTILS - FUNCIONES MODULARES COMUNES
 * ============================================================
 * Archivo centralizado para funciones repetidas en múltiples módulos AJAX
 * Compatible con el código existente - NO modifica la lógica actual
 */

// ============================================================
// LIMPIEZA DE MODALES Y BACKDROPS
// ============================================================

/**
 * Limpia backdrops y restaura el scroll del body
 * Usar después de cerrar modales para evitar pantallas negras
 */
function limpiarBackdrop() {
    setTimeout(() => {
        $('.swal2-container').remove();
        $('.modal-backdrop').remove();
        $('body').removeClass('modal-open');
        $('body').css('padding-right', '');
    }, 1500);
}

/**
 * Evento global para limpiar backdrops al cerrar cualquier modal
 * Se ejecuta automáticamente
 */
$(document).on('hidden.bs.modal', function() {
    limpiarBackdrop();
});

// ============================================================
// VALIDACIONES DE CAMPOS
// ============================================================

/**
 * Muestra el estado de validación de un campo
 * @param {jQuery} $input - Campo a validar
 * @param {string} estado - 'ok' o 'error'
 * @param {string} mensaje - Mensaje de error (opcional)
 */
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

/**
 * Marca un campo como inválido
 * @param {jQuery} $input - Campo a marcar
 * @param {string} mensaje - Mensaje de error
 */
function markInvalid($input, mensaje) {
    mostrarEstado($input, 'error', mensaje);
}

/**
 * Marca un campo como válido
 * @param {jQuery} $input - Campo a marcar
 */
function markValid($input) {
    mostrarEstado($input, 'ok');
}

/**
 * Limpia todas las validaciones de un campo
 * @param {jQuery} $input - Campo a limpiar
 */
function clearValidation($input) {
    $input.removeClass('is-valid is-invalid');
    $input.next('.invalid-feedback').remove();
}

/**
 * Hace foco en el primer campo inválido de un formulario
 * @param {jQuery} $form - Formulario
 */
function firstInvalidFocus($form) {
    const $firstInvalid = $form.find('.is-invalid').first();
    if ($firstInvalid.length) {
        $firstInvalid.focus();
    }
}

// ============================================================
// LIMPIEZA DE FORMULARIOS
// ============================================================

/**
 * Limpia completamente un formulario (resetea y elimina validaciones)
 * @param {jQuery} $form - Formulario a limpiar
 */
function limpiarFormulario($form) {
    $form[0].reset();
    $form.find('.is-valid, .is-invalid').removeClass('is-valid is-invalid');
    $form.find('.invalid-feedback').remove();
}

/**
 * Limpia el formulario de registro al cerrar el modal
 * Uso: $('#miModal').on('hidden.bs.modal', function() { limpiarFormularioModal($(this), '#miForm'); });
 */
function limpiarFormularioModal($modal, formSelector) {
    const $form = $modal.find(formSelector);
    if ($form.length) {
        limpiarFormulario($form);
    }
}

// ============================================================
// DETECCIÓN DE CAMBIOS EN FORMULARIOS
// ============================================================

/**
 * Guarda los datos originales de un formulario en formato serializado
 * @param {jQuery} $form - Formulario
 * @returns {string} Datos serializados
 */
function guardarDatosOriginales($form) {
    return $form.serialize();
}

/**
 * Guarda los datos originales de un formulario en formato objeto
 * @param {jQuery} $form - Formulario
 * @returns {Object} Objeto con los valores originales
 */
function guardarDatosOriginalesObjeto($form) {
    const datos = {};
    $form.find('input, select, textarea').each(function() {
        const name = $(this).attr('name');
        if (name) datos[name] = $(this).val();
    });
    return datos;
}

/**
 * Detecta si hubo cambios en el formulario comparando con datos originales
 * @param {jQuery} $form - Formulario
 * @param {Object} datosOriginales - Objeto con los valores originales
 * @returns {boolean} true si hay cambios
 */
function detectarCambios($form, datosOriginales) {
    let hayCambios = false;
    $form.find('input, select, textarea').each(function() {
        const name = $(this).attr('name');
        if (name && datosOriginales[name] !== $(this).val()) {
            hayCambios = true;
            return false; // break
        }
    });
    return hayCambios;
}

/**
 * Detecta cambios solo en campos con clase específica
 * @param {jQuery} $form - Formulario
 * @param {Object} datosOriginales - Objeto con valores originales
 * @param {string} claseSelector - Selector de clase (ej: '.campo-editable')
 * @returns {boolean}
 */
function detectarCambiosEnCampos($form, datosOriginales, claseSelector) {
    let hayCambios = false;
    $form.find(claseSelector).each(function() {
        const name = $(this).attr('name');
        if (name && datosOriginales[name] !== $(this).val()) {
            hayCambios = true;
            return false;
        }
    });
    return hayCambios;
}

// ============================================================
// RECARGA DE TABLAS DATATABLE
// ============================================================

/**
 * Destruye un DataTable de forma segura
 * @param {string} tableSelector - Selector de la tabla (ej: '#miTabla')
 */
function destruirDataTable(tableSelector) {
    if ($.fn.DataTable.isDataTable(tableSelector)) {
        $(tableSelector).DataTable().destroy();
    }
}

/**
 * Inicializa o reinicializa un DataTable con configuración estándar
 * @param {string} tableSelector - Selector de la tabla
 * @param {Object} opciones - Opciones adicionales (opcional)
 */
function inicializarDataTable(tableSelector, opciones = {}) {
    const configDefault = {
        responsive: true,
        autoWidth: false,
        language: { url: 'assets/Temple/src/plugins/datatables/js/es_es.json' },
        columnDefs: [{ targets: 'datatable-nosort', orderable: false }]
    };
    
    const config = { ...configDefault, ...opciones };
    $(tableSelector).DataTable(config);
}

/**
 * Restaura los colores de los íconos de acción en la tabla
 * (Los data-color se pierden al recargar dinámicamente)
 */
function restaurarColoresIconos() {
    $('.table-actions a').each(function() {
        const color = $(this).data('color');
        if (color) $(this).find('i').css('color', color);
    });
}

/**
 * Función genérica para recargar tabla con modales
 * @param {Object} config - Configuración
 * @param {string} config.url - URL para obtener el HTML actualizado
 * @param {string} config.tableSelector - Selector de la tabla
 * @param {string} config.modalSelector - Selector de modales a reemplazar
 * @param {Function} config.onSuccess - Callback adicional (opcional)
 * @param {Function} config.onError - Callback de error (opcional)
 */
function recargarTablaConModales(config) {
    destruirDataTable(config.tableSelector);

    $.ajax({
        url: config.url,
        type: 'GET',
        success: function(html) {
            const $html = $(html);

            // Reemplazar tbody
            const nuevoTbody = $html.find(`${config.tableSelector} tbody`).html();
            $(`${config.tableSelector} tbody`).html(nuevoTbody);

            // Reemplazar modales
            const nuevosModales = $html.find(config.modalSelector);
            $(config.modalSelector).remove();
            $('body').append(nuevosModales);

            // Reinicializar DataTable
            inicializarDataTable(config.tableSelector);

            // Restaurar colores
            restaurarColoresIconos();

            // Callback adicional
            if (typeof config.onSuccess === 'function') {
                config.onSuccess();
            }
        },
        error: function() {
            const mensaje = config.mensajeError || 'No se pudo recargar la tabla.';
            Swal.fire('Error', mensaje, 'error');
            
            if (typeof config.onError === 'function') {
                config.onError();
            }
        }
    });
}

// ============================================================
// ALERTAS SWEETALERT ESTANDARIZADAS
// ============================================================

/**
 * Muestra alerta de éxito
 * @param {string} mensaje - Mensaje a mostrar
 * @param {number} timer - Tiempo en ms (default: 1500)
 */
function alertaExito(mensaje, timer = 1500) {
    Swal.fire({
        icon: 'success',
        title: 'Éxito',
        text: mensaje,
        timer: timer,
        showConfirmButton: false
    });
}

/**
 * Muestra alerta de error
 * @param {string} mensaje - Mensaje a mostrar
 */
function alertaError(mensaje) {
    Swal.fire({
        icon: 'error',
        title: 'Error',
        text: mensaje
    });
}

/**
 * Muestra alerta informativa sin cambios detectados
 */
function alertaSinCambios() {
    Swal.fire({
        icon: 'info',
        title: 'Sin cambios',
        text: 'No se detectaron modificaciones en los datos.',
        timer: 2000,
        showConfirmButton: false
    });
}

/**
 * Muestra alerta de advertencia
 * @param {string} mensaje - Mensaje a mostrar
 */
function alertaAdvertencia(mensaje) {
    Swal.fire({
        icon: 'warning',
        title: 'Atención',
        text: mensaje,
        timer: 2000,
        showConfirmButton: false
    });
}

/**
 * Alerta genérica basada en respuesta del servidor
 * @param {Object} respuesta - Objeto de respuesta con estado y mensaje
 * @param {number} timerExito - Timer para éxito (default: 1500)
 */
function alertaRespuesta(respuesta, timerExito = 1500) {
    const esExito = respuesta.estado === 'success' || respuesta.success === true;
    Swal.fire({
        icon: esExito ? 'success' : 'error',
        title: esExito ? 'Éxito' : 'Error',
        text: respuesta.mensaje || respuesta.message,
        timer: esExito ? timerExito : 2500,
        showConfirmButton: !esExito
    });
}

// ============================================================
// FUNCIONES PARA CHECKBOXES Y RADIOS
// ============================================================

/**
 * Inicializa checkboxes con control de botón de acción
 * @param {string} checkboxSelector - Selector de los checkboxes
 * @param {string} buttonId - ID del botón a controlar
 * @param {number} cantidadRequerida - Cantidad de checkboxes requeridos (default: 1)
 */
function inicializarCheckboxesConBoton(checkboxSelector, buttonId, cantidadRequerida = 1) {
    const checkboxes = document.querySelectorAll(checkboxSelector);
    const btn = document.getElementById(buttonId);

    // Limpiar listeners previos clonando elementos
    checkboxes.forEach(chk => {
        const newChk = chk.cloneNode(true);
        chk.parentNode.replaceChild(newChk, chk);
    });

    // Agregar nuevos listeners
    document.querySelectorAll(checkboxSelector).forEach(chk => {
        chk.addEventListener('change', function() {
            const selected = document.querySelectorAll(`${checkboxSelector}:checked`);
            if (btn) {
                btn.disabled = (selected.length !== cantidadRequerida);
            }
        });
    });

    // Actualizar estado inicial
    if (btn) {
        const selected = document.querySelectorAll(`${checkboxSelector}:checked`);
        btn.disabled = (selected.length !== cantidadRequerida);
    }
}

/**
 * Desmarca todos los checkboxes de un selector
 * @param {string} checkboxSelector - Selector de los checkboxes
 */
function desmarcarCheckboxes(checkboxSelector) {
    $(checkboxSelector).prop('checked', false);
}

// ============================================================
// ENVÍO DE FORMULARIOS VIA AJAX
// ============================================================

/**
 * Configuración estándar para envío de formulario con AJAX
 * @param {Object} config - Configuración
 * @param {jQuery} config.$form - Formulario jQuery
 * @param {string} config.modalId - ID del modal a cerrar
 * @param {Function} config.onSuccess - Callback de éxito
 * @param {Function} config.onError - Callback de error (opcional)
 * @param {boolean} config.useFormData - Usar FormData (default: false)
 */
function enviarFormularioAjax(config) {
    const datos = config.useFormData ? new FormData(config.$form[0]) : config.$form.serialize();
    const ajaxConfig = {
        url: config.$form.attr('action'),
        type: 'POST',
        data: datos,
        dataType: 'json'
    };

    if (config.useFormData) {
        ajaxConfig.contentType = false;
        ajaxConfig.processData = false;
    }

    ajaxConfig.success = function(res) {
        $(`#${config.modalId}`).modal('hide');

        setTimeout(() => {
            if (typeof config.onSuccess === 'function') {
                config.onSuccess(res);
            }
        }, 300);
    };

    ajaxConfig.error = function(xhr) {
        if (typeof config.onError === 'function') {
            config.onError(xhr);
        } else {
            alertaError('No se pudo procesar la solicitud.');
        }
    };

    $.ajax(ajaxConfig);
}

// ============================================================
// FUNCIONES GLOBALES DE UTILIDAD
// ============================================================

/**
 * Función global para asignar ID a eliminar (patrón común)
 * @param {string} inputId - ID del input hidden
 * @param {string|number} valor - Valor a asignar
 */
function setDeleteId(inputId, valor) {
    document.getElementById(inputId).value = valor;
}

/**
 * Aplicar colores a iconos desde data-attributes
 * Útil después de clonar o recargar elementos
 */
function aplicarColoresIconos() {
    restaurarColoresIconos();
}

// ============================================================
// VALIDACIONES COMUNES
// ============================================================

/**
 * Valida nombre de campo (letras, números, espacios y caracteres especiales comunes)
 * @param {string} valor - Valor a validar
 * @returns {Object} {ok: boolean, msg: string}
 */
function validarNombreCampo(valor) {
    if (!valor || valor.trim() === '') {
        return { ok: false, msg: 'Este campo es obligatorio' };
    }
    const regex = /^[a-zA-Z0-9\sáéíóúÁÉÍÓÚñÑ.,\-()_]+$/;
    if (!regex.test(valor)) {
        return { ok: false, msg: 'Solo se permiten letras, números y caracteres (,.-()_)' };
    }
    return { ok: true, msg: '' };
}

/**
 * Valida dirección (similar a nombre pero más permisivo)
 * @param {string} valor - Valor a validar
 * @returns {Object} {ok: boolean, msg: string}
 */
function validarDireccionCampo(valor) {
    if (!valor || valor.trim() === '') {
        return { ok: false, msg: 'La dirección es obligatoria' };
    }
    const regex = /^[a-zA-Z0-9\sáéíóúÁÉÍÓÚñÑ.,\-()_#/]+$/;
    if (!regex.test(valor)) {
        return { ok: false, msg: 'Dirección contiene caracteres no permitidos' };
    }
    return { ok: true, msg: '' };
}

/**
 * Valida que un select tenga valor seleccionado
 * @param {jQuery} $select - Select a validar
 * @param {string} nombreCampo - Nombre del campo para mensaje
 * @returns {boolean}
 */
function validarSelectRequerido($select, nombreCampo) {
    const valor = $select.val();
    if (!valor) {
        mostrarEstado($select, 'error', `Debe seleccionar ${nombreCampo}`);
        return false;
    }
    mostrarEstado($select, 'ok');
    return true;
}

// ============================================================
// EXPORTAR FUNCIONES GLOBALMENTE
// ============================================================

// Las funciones ya están disponibles globalmente al estar en window scope
// pero podemos crear un objeto namespace opcional:
window.AjaxUtils = {
    // Limpieza
    limpiarBackdrop,
    limpiarFormulario,
    limpiarFormularioModal,
    
    // Validaciones
    mostrarEstado,
    markInvalid,
    markValid,
    clearValidation,
    firstInvalidFocus,
    validarNombreCampo,
    validarDireccionCampo,
    validarSelectRequerido,
    
    // Cambios
    guardarDatosOriginales,
    guardarDatosOriginalesObjeto,
    detectarCambios,
    detectarCambiosEnCampos,
    
    // DataTables
    destruirDataTable,
    inicializarDataTable,
    restaurarColoresIconos,
    recargarTablaConModales,
    
    // Alertas
    alertaExito,
    alertaError,
    alertaSinCambios,
    alertaAdvertencia,
    alertaRespuesta,
    
    // Checkboxes
    inicializarCheckboxesConBoton,
    desmarcarCheckboxes,
    
    // AJAX
    enviarFormularioAjax,
    
    // Utilidades
    setDeleteId,
    aplicarColoresIconos
};

console.log('✅ ajax_utils.js cargado correctamente');

/**
 * Realiza una petición fetch a la URL especificada.
 *
 * @param {string} url - La URL a la que se enviará la petición.
 * @param {string} method - El método HTTP (e.g., 'POST', 'GET').
 * @param {FormData|object} body - El cuerpo de la petición.
 * @returns {Promise<any>} - Una promesa que se resuelve con los datos de la respuesta.
 * @throws {Error} - Lanza un error si la petición falla.
 */
async function sendRequest(url, method = 'POST', body) {
    const options = {
        method: method,
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    };

    if (body) {
        options.body = body;
    }

    try {
        const response = await fetch(url, options);
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        return await response.json();
    } catch (error) {
        console.error('Fetch Error:', error);
        throw error;
    }
}