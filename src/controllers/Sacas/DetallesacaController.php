<?php
use RapiExpress\Models\Saca;
use RapiExpress\Models\Paquete;
use RapiExpress\Models\DetalleSaca;




function detallesaca_index()
{
    if (!isset($_SESSION['usuario'])) {
        header('Location: index.php');
        exit();
    }

    $saca = null;
    $paquetesEnSaca = [];
    $paquetesDisponibles = [];

    $sacaModel = new Saca();
    $detalleModel = new DetalleSaca();
    $paqueteModel = new Paquete();

    // Si se proporciona ID de saca
    if (isset($_GET['id']) && is_numeric($_GET['id'])) {
        $idSaca = (int) $_GET['id'];

        // Buscar la saca
        $saca = $sacaModel->obtenerPorId($idSaca);

        if ($saca) {
            // Paquetes que están dentro de la saca
            $paquetesEnSaca = $detalleModel->obtenerPorSaca($idSaca);

            // Paquetes que no tienen saca asignada (disponibles)
            $paquetesDisponibles = $paqueteModel->obtenerSinSaca();

            // Actualizar peso total de la saca
            $detalleModel->actualizarPesoSaca($idSaca);
        } else {
            $_SESSION['mensaje'] = 'La saca no existe o fue eliminada.';
            $_SESSION['tipo_mensaje'] = 'error';
        }
    }

    include __DIR__ . '/../../views/saca/detalle_saca.php';
}


function detallesaca_agregar() {
    $idSaca = $_POST['ID_Saca'];
    $idPaquete = $_POST['ID_Paquete'];

    $detalleModel = new \RapiExpress\Models\DetalleSaca();
    $resultado = $detalleModel->agregarPaquete($idSaca, $idPaquete);

    switch ($resultado) {
        case 'paquete_ya_asignado':
            $_SESSION['mensaje'] = "❗ Este paquete ya está asignado a otra saca.";
            $_SESSION['tipo_mensaje'] = "warning";
            break;

        case 'paquete_no_apto':
            $_SESSION['mensaje'] = "⚠️ Solo se pueden agregar paquetes con estado 'En tránsito'.";
            $_SESSION['tipo_mensaje'] = "error";
            break;

        case 'sucursal_diferente':
            $_SESSION['mensaje'] = "🚫 El paquete pertenece a otra sucursal. No se puede agregar.";
            $_SESSION['tipo_mensaje'] = "error";
            break;

        case 'paquete_no_existe':
            $_SESSION['mensaje'] = "❌ El paquete no existe.";
            $_SESSION['tipo_mensaje'] = "error";
            break;

        case 'saca_no_existe':
            $_SESSION['mensaje'] = "❌ La saca seleccionada no existe.";
            $_SESSION['tipo_mensaje'] = "error";
            break;

        case 'agregado_exitoso':
            $_SESSION['mensaje'] = "✅ Paquete agregado exitosamente a la saca.";
            $_SESSION['tipo_mensaje'] = "success";
            break;

        default:
            $_SESSION['mensaje'] = "❌ Error al agregar el paquete.";
            $_SESSION['tipo_mensaje'] = "error";
    }

    header("Location: index.php?c=detallesaca&a=index&id={$idSaca}");
    exit();
}



function detallesaca_quitar() {
    $detalleModel = new DetalleSaca();

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $idPaquete = (int)$_POST['ID_Paquete'];
        $idSaca = (int)$_POST['ID_Saca'];

        $detalleModel->quitarPaquete($idPaquete, $idSaca);

        $_SESSION['mensaje'] = 'Paquete quitado correctamente.';
        $_SESSION['tipo_mensaje'] = 'success';
        header("Location: index.php?c=detallesaca&a=index&id={$idSaca}");
        exit();
    }
}

