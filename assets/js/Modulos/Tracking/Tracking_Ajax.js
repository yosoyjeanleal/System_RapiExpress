$(document).ready(function() {

    $('#formBuscarTracking').on('submit', function(e) {
        e.preventDefault();
        const tracking = $('input[name="tracking"]').val().trim();

        if (!tracking) return;

        $.ajax({
            url: 'index.php?c=seguimiento&a=buscar',
            type: 'POST',
            data: { tracking },
            dataType: 'json',
            success: function(respuesta) {
                let html = '';

                if (respuesta.prealerta) {
                    html += `
                        <div class="card-box mb-30">
                            <div class="pd-20">
                                <h5 class="mb-3">Prealerta encontrada</h5>
                                <div class="table-responsive">
                                    <table class="table table-striped">
                                        <tr><th>Cliente:</th><td>${respuesta.prealerta.Nombres_Cliente} ${respuesta.prealerta.Apellidos_Cliente}</td></tr>
                                        <tr><th>Tienda:</th><td>${respuesta.prealerta.Tienda_Nombre}</td></tr>
                                        <tr><th>Piezas:</th><td>${respuesta.prealerta.Prealerta_Piezas}</td></tr>
                                        <tr><th>Peso:</th><td>${respuesta.prealerta.Prealerta_Peso}</td></tr>
                                        <tr><th>Descripción:</th><td>${respuesta.prealerta.Prealerta_Descripcion}</td></tr>
                                        <tr><th>Estado:</th><td><span class="badge badge-primary">${respuesta.prealerta.Estado}</span></td></tr>
                                    </table>
                                </div>
                            </div>
                        </div>`;
                } else if (respuesta.paquete) {
                    html += `
                        <div class="card-box mb-30">
                            <div class="pd-20">
                                <h5 class="mb-3">Paquete encontrado</h5>
                                <div class="row">
                                    <div class="col-md-8 col-sm-12">
                                        <div class="table-responsive">
                                            <table class="table table-striped">
                                                <tr><th>Cliente:</th><td>${respuesta.paquete.Nombres_Cliente} ${respuesta.paquete.Apellidos_Cliente}</td></tr>
                                                <tr><th>Categoría:</th><td>${respuesta.paquete.Categoria_Nombre}</td></tr>
                                                <tr><th>Courier:</th><td>${respuesta.paquete.Courier_Nombre}</td></tr>
                                                <tr><th>Sucursal:</th><td>${respuesta.paquete.Sucursal_Nombre}</td></tr>
                                                <tr><th>Peso:</th><td>${respuesta.paquete.Paquete_Peso}</td></tr>
                                                <tr><th>Descripción:</th><td>${respuesta.paquete.Prealerta_Descripcion}</td></tr>
                                                <tr><th>Estado:</th><td><span class="badge badge-primary">${respuesta.paquete.Estado}</span></td></tr>
                                            </table>
                                        </div>
                                    </div>
                                    <div class="col-md-4 col-sm-12 text-center">
                                        ${respuesta.paquete.Qr_code ? 
                                            `<div class="border p-3 bg-light rounded">
                                                <img src="src/storage/qr/${respuesta.paquete.Qr_code}" class="img-fluid" style="max-width: 200px;">
                                                <p class="mt-2 text-muted">Escanea este código</p>
                                            </div>` :
                                            `<div class="border p-3 bg-light rounded text-center">
                                                <i class="dw dw-box-2" style="font-size: 5rem; color: #1845A2;"></i>
                                                <p class="mt-2">Paquete registrado</p>
                                            </div>`
                                        }
                                    </div>
                                </div>
                            </div>
                        </div>`;
                } else {
                    html += `
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            No se encontró ninguna prealerta o paquete con el código "${tracking}".
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>`;
                }

                $('#resultadoTracking').html(html);
            },
            error: function() {
                Swal.fire('Error', 'No se pudo buscar el tracking.', 'error');
            }
        });
    });

});