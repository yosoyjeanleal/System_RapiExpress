<?php
use RapiExpress\Models\Manifiesto;
use RapiExpress\Models\Saca;
use RapiExpress\Models\Paquete;


function manifiesto_index() {
    $model = new Manifiesto();
    $manifiestos = $model->obtenerTodos();
    include __DIR__ . '/../../views/manifiesto/manifiesto.php';
}
function manifiesto_generar() {
    if (!isset($_POST['ID_Saca'])) {
        header('Location: index.php?c=manifiesto');
        exit();
    }

    $idSaca = (int) $_POST['ID_Saca'];
    $usuarioId = $_SESSION['ID_Usuario'] ?? 1;

    $sacaModel = new Saca();
    $saca = $sacaModel->obtenerPorId($idSaca);

    if (!$saca) {
        $_SESSION['mensaje'] = 'Saca no encontrada';
        $_SESSION['tipo_mensaje'] = 'error';
        header('Location: index.php?c=manifiesto');
        exit();
    }

    $manifiestoModel = new Manifiesto();
    $paquetes = $manifiestoModel->obtenerPaquetesDeSaca($idSaca);

    // === Recomendado: incluir TCPDF (ajusta la ruta según tu proyecto) ===
    // Si usas composer autoload, comentarlo y usa require __DIR__ . '/../../vendor/autoload.php';
    if (!class_exists('TCPDF')) {
        $tcpdfPath = __DIR__ . '/../../vendor/tecnickcom/tcpdf/tcpdf.php'; // ejemplo
        if (file_exists($tcpdfPath)) {
            require_once $tcpdfPath;
        } else {
            // intento alternativo: ruta común
            $alt = __DIR__ . '/../../libs/tcpdf/tcpdf.php';
            if (file_exists($alt)) require_once $alt;
        }
    }

    if (!class_exists('TCPDF')) {
        error_log("TCPDF no encontrada. Verifica instalación / include path.");
        $_SESSION['mensaje'] = 'Error interno: librería PDF no disponible.';
        $_SESSION['tipo_mensaje'] = 'error';
        header('Location: index.php?c=manifiesto');
        exit();
    }

    try {
        // usa un solo timestamp para archivo y ruta en BD
        $ts = date('YmdHis');

        $pdf = new TCPDF('L', 'mm', 'A4', true, 'UTF-8');
        $pdf->SetCreator('RapiExpress');
        $pdf->SetAuthor('RapiExpress');
        $pdf->SetTitle("Manifiesto - ".$saca['Codigo_Saca']);
        $pdf->SetMargins(10, 10, 10);
        $pdf->AddPage();
        $pdf->SetFont('helvetica', '', 9);

        // Encabezado HTML simple (usa writeHTML)
        $html = '<h1 style="text-align:center;">MANIFIESTO DE SACA</h1>';
        $html .= '<table style="width:100%; border:none; margin-bottom:8px;">
            <tr><td style="border:none;"><strong>Código Saca:</strong> '.htmlspecialchars($saca['Codigo_Saca']).'</td>
                <td style="border:none;"><strong>Estado:</strong> '.htmlspecialchars($saca['Estado']).'</td></tr>
            <tr><td style="border:none;"><strong>Peso Total:</strong> '.htmlspecialchars($saca['Peso_Total']).' kg</td>
                <td style="border:none;"><strong>Fecha:</strong> '.date('d/m/Y H:i').'</td></tr>
            <tr><td colspan="2" style="border:none;"><strong>Generado por:</strong> '.($_SESSION['Nombres_Usuario'].' '.$_SESSION['Apellidos_Usuario']).'</td></tr>
        </table>';
        $pdf->writeHTML($html, true, false, true, false, '');

        // Cabecera tabla (ajusta si necesitas)
        $headers = ['Tracking','Cliente','Teléfono','Dirección','Instrumento','Descripción','Categoría','Courier','Sucursal','Peso','Estado'];
        $colWidths = [25,40,25,50,35,60,30,30,30,18,25]; // suma aproximada; ajusta según A4 landscape

        // pinta cabecera
        $pdf->SetFillColor(52, 152, 219);
        $pdf->SetTextColor(255);
        $pdf->SetFont('', 'B');
        foreach ($headers as $i => $h) {
            $w = $colWidths[$i] ?? 20;
            $pdf->Cell($w, 7, $h, 1, 0, 'C', 1);
        }
        $pdf->Ln();

        $pdf->SetFillColor(224,235,255);
        $pdf->SetTextColor(0);
        $pdf->SetFont('', '');
        $fill = false;
        $pesoTotal = 0;
        $totalPaquetes = is_array($paquetes) ? count($paquetes) : 0;

        if ($totalPaquetes === 0) {
            // fila vacía
            $pdf->Cell(array_sum($colWidths), 6, 'No hay paquetes en esta saca', 1, 1, 'C');
        } else {
            foreach ($paquetes as $p) {
                $peso = isset($p['Paquete_Peso']) ? (float)$p['Paquete_Peso'] : 0;
                $pesoTotal += $peso;

                $pdf->MultiCell($colWidths[0],6,($p['Tracking'] ?? 'N/A'),1,'C',$fill,0);
                $pdf->MultiCell($colWidths[1],6,trim(($p['Nombres_Cliente'] ?? '').' '.($p['Apellidos_Cliente'] ?? '')) ,1,'L',$fill,0);
                $pdf->MultiCell($colWidths[2],6,($p['Telefono_Cliente'] ?? 'N/A'),1,'L',$fill,0);
                $pdf->MultiCell($colWidths[3],6,($p['Direccion_Cliente'] ?? 'N/A'),1,'L',$fill,0);
                $pdf->MultiCell($colWidths[4],6,($p['Nombre_Instrumento'] ?? 'N/A'),1,'L',$fill,0);
                $pdf->MultiCell($colWidths[5],6,($p['Prealerta_Descripcion'] ?? 'Sin descripción'),1,'L',$fill,0);
                $pdf->MultiCell($colWidths[6],6,($p['Categoria_Nombre'] ?? 'N/A'),1,'C',$fill,0);
                $pdf->MultiCell($colWidths[7],6,($p['Courier_Nombre'] ?? 'N/A'),1,'C',$fill,0);
                $pdf->MultiCell($colWidths[8],6,($p['Sucursal_Nombre'] ?? 'N/A'),1,'C',$fill,0);
                $pdf->MultiCell($colWidths[9],6,number_format($peso,2),1,'R',$fill,0);
                $pdf->MultiCell($colWidths[10],6,($p['Estado'] ?? 'N/A'),1,'C',$fill,1); // ln=1 al final de fila
                $fill = !$fill;
            }
        }

        // Totales
        $pdf->SetFont('', 'B');
        $sumWidth = array_sum(array_slice($colWidths, 0, 10));
        $pdf->Cell($sumWidth, 7, 'TOTALES', 1, 0, 'R', 1);
        $pdf->Cell($colWidths[9], 7, number_format($pesoTotal,2).' kg', 1, 0, 'R', 1);
        $pdf->Cell($colWidths[10], 7, $totalPaquetes.' paquetes', 1, 1, 'C', 1);
        $pdf->Ln(6);

        // Firmas
        $pdf->Cell(0,10,"_________________________",0,1,'L');
        $pdf->Cell(0,5,"Firma del Remitente",0,1,'L');
        $pdf->Cell(0,10,"_________________________",0,1,'R');
        $pdf->Cell(0,5,"Firma del Receptor",0,1,'R');

        // Guardar PDF: ruta absoluta en servidor
        $ruta = __DIR__ . '/../src/storage/manifiestos/';
        if (!file_exists($ruta)) {
            if (!mkdir($ruta, 0777, true)) {
                throw new Exception("No se pudo crear la carpeta: $ruta");
            }
        }
        if (!is_writable($ruta)) {
            // intenta cambiar permisos, pero puede fallar según usuario
            @chmod($ruta, 0777);
            if (!is_writable($ruta)) {
                throw new Exception("La carpeta $ruta no es escribible por PHP.");
            }
        }

        $nombreArchivo = 'Manifiesto_Saca_'.$saca['Codigo_Saca'].'_'.$ts.'.pdf';
        $rutaCompleta = $ruta . $nombreArchivo;

        // guarda archivo en disco
        $pdf->Output($rutaCompleta, 'F');

        // ruta pública relativa que guardas en BD (ajusta si tu public path es distinto)
        $rutaRelativa = 'storage/manifiestos/'.$nombreArchivo;

        // registrar en BD
        $registrado = $manifiestoModel->registrar($idSaca, $usuarioId, $rutaRelativa);
        if (!$registrado) {
            error_log("Error al registrar manifiesto en DB para saca $idSaca");
            $_SESSION['mensaje'] = 'Manifiesto generado pero ocurrió un error al registrar en la base de datos.';
            $_SESSION['tipo_mensaje'] = 'warning';
        } else {
            $_SESSION['mensaje'] = 'Manifiesto generado exitosamente con '.$totalPaquetes.' paquetes';
            $_SESSION['tipo_mensaje'] = 'success';
        }

        header('Location: index.php?c=manifiesto');
        exit();

    } catch (Exception $e) {
        error_log("Error generando manifiesto: " . $e->getMessage());
        $_SESSION['mensaje'] = 'Error generando el manifiesto: ' . $e->getMessage();
        $_SESSION['tipo_mensaje'] = 'error';
        header('Location: index.php?c=manifiesto');
        exit();
    }
}

function manifiesto_eliminar() {
    if (!isset($_POST['ID_Manifiesto'])) {
        header('Location: index.php?c=manifiesto');
        exit();
    }

    $id = (int) $_POST['ID_Manifiesto'];
    $model = new Manifiesto();
    $model->eliminar($id);

    $_SESSION['mensaje'] = 'Manifiesto eliminado';
    $_SESSION['tipo_mensaje'] = 'success';
    header('Location: index.php?c=manifiesto');
    exit();
}
