<?php
// Recibimos datos del paquete por GET
$tracking   = $_GET['tracking'] ?? '';
$cliente    = $_GET['cliente'] ?? '';
$instrumento = $_GET['instrumento'] ?? '';
$categoria  = $_GET['categoria'] ?? '';
$sucursal   = $_GET['sucursal'] ?? '';
$courier    = $_GET['courier'] ?? '';
$descripcion= $_GET['descripcion'] ?? '';
$peso       = $_GET['peso'] ?? '';
$qr         = $_GET['qr'] ?? '';

// ✅ Timestamp para evitar caché
$timestamp = $_GET['t'] ?? time();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Etiqueta Paquete <?= htmlspecialchars($tracking) ?></title>
    <style>
        @media print {
            body {
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
                background-color: black !important;
            }

            * {
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }
        }
        
        @page {
            margin: 0;
            size: 4in 6in;
        }

        /* Vista en pantalla */
        body {
            margin: 0;
            padding: 0;
            font-family: Arial, sans-serif;
            background: #f0f0f0;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }

        .label {
            width: 4in;
            height: 6in;
            border: 2px solid #000;
            box-sizing: border-box;
            display: flex;
            flex-direction: column;
            margin: auto;
            padding: 8px;
            background: #fff;
        }

        .header {
            background-color: #000;
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            height: 60px;
            box-sizing: border-box;
        }

        .logo img {
            max-height: 50px;
            object-fit: contain;
        }

        .content {
            flex: 1;
            padding: 8px;
            display: flex;
            flex-direction: column;
            font-size: 12px;
        }

        .field { margin-bottom: 6px; }
        .field-label { font-size: 12px; font-weight: bold; color: #000; }
        .field-value { font-size: 11px; color: #333; margin-top: 2px; }

        .separator { border-top: 1px solid #000; margin: 8px 0; }

        .location-section { margin-bottom: 8px; }
        .location-title { font-size: 11px; font-weight: bold; text-align: center; margin-bottom: 6px; }

        .table-container { border: 1px solid #000; margin: 0 auto; width: 100%; }
        .table-header, .table-row {
            display: grid;
            grid-template-columns: 1fr 80px 60px;
        }
        .table-header { border-bottom: 1px solid #000; font-weight: bold; font-size: 11px; }
        .table-cell {
            padding: 4px;
            border-right: 1px solid #000;
            text-align: center;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .table-cell:last-child { border-right: none; }
        .table-row { border-bottom: 1px solid #000; min-height: 40px; }
        .table-row:last-child { border-bottom: none; }

        .large-text { font-size: 18px; font-weight: bold; display: flex; align-items: center; justify-content: center; }

        .qr { text-align: center; margin-top: 6px; }
        .qr img { width: 120px; height: 120px; }

        /* Estilos solo al imprimir */
        @media print {
            body {
                display: block;
                margin: 0;
                padding: 0;
                background: white;
            }
            .label {
                margin: 0;
                border: 2px solid #000;
                page-break-inside: avoid;
            }
        }
    </style>
</head>
<body>
    <div class="label">
        <div class="header">
            <div class="logo">
                <img src="../../../assets/img/logo-rapi.ico" alt="RAPIEXPRESS Logo">
            </div>
        </div>

        <div class="content">
            <!-- Datos remitente fijo -->
            <div class="field">
                <div class="field-label">FROM:</div>
                <div class="field-value">7990 W 20TH AVE Ste 27<br>Hialeah, FL 33016-1831</div>
            </div>

            <div class="separator"></div>

            <!-- Consignatario dinámico -->
            <div class="field">
                <div class="field-label">CONSIGNATAIRE:</div>
                <div class="field-value"><?= htmlspecialchars($cliente) ?></div>
            </div>
            
            <div class="field">               
                <div class="field-label">RECIBE:</div>
                <div class="field-value"><?= htmlspecialchars($instrumento) ?></div>
            </div>

            <div class="separator"></div>

            <!-- Sección ubicación -->
            <div class="location-section">
                <div class="location-title">LOCATION GUAYAQUIL - ECUADOR</div>
                <div class="table-container">
                    <div class="table-header">
                        <div class="table-cell">TRACKING</div>
                        <div class="table-cell">WEIGHT</div>
                        <div class="table-cell">KG</div>
                    </div>
                    <div class="table-row">
                        <div class="table-cell large-text"><?= htmlspecialchars($tracking) ?></div>
                        <div class="table-cell large-text"><?= htmlspecialchars($peso) ?></div>
                        <div class="table-cell large-text">KG</div>
                    </div>
                </div>
            </div>

            <!-- Más datos -->
            <div class="field"><b>Categoría:</b> <?= htmlspecialchars($categoria) ?></div>
            <div class="field"><b>Sucursal:</b> <?= htmlspecialchars($sucursal) ?></div>
            <div class="field"><b>Courier:</b> <?= htmlspecialchars($courier) ?></div>
            <div class="field"><b>Descripción:</b> <?= htmlspecialchars($descripcion) ?></div>

            <!-- QR dinámico con anti-caché -->
            <div class="qr">
                <?php if (!empty($qr)): ?>
                    <img src="../../../src/storage/qr/<?= htmlspecialchars($qr) ?>?v=<?= $timestamp ?>" alt="QR Code">
                <?php else: ?>
                    <p>[Sin QR]</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>
</html>