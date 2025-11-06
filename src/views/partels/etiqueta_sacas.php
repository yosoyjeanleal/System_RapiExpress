<?php
// Recibimos datos de la saca por GET
$codigo   = $_GET['codigo'] ?? '';
$usuario  = $_GET['usuario'] ?? '';
$sucursal = $_GET['sucursal'] ?? '';
$estado   = $_GET['estado'] ?? '';
$peso     = $_GET['peso'] ?? '';
$cantidad = $_GET['cantidad'] ?? '';
$fecha    = $_GET['fecha'] ?? '';
$idSaca   = $_GET['id'] ?? '';

// Timestamp para evitar caché
$timestamp = $_GET['t'] ?? time();

// ✅ CONSTRUIR URL BASE DINÁMICA
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https://' : 'http://';
$host = $_SERVER['HTTP_HOST'];
$baseUrl = $protocol . $host;

// ✅ Obtener directorio base (si está en subcarpeta)
$scriptName = dirname($_SERVER['SCRIPT_NAME']);
if ($scriptName !== '/') {
    $baseUrl .= $scriptName;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Etiqueta Saca <?= htmlspecialchars($codigo) ?></title>
    <style>
        @media print {
            body {
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
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

        .saca-section { margin-bottom: 8px; }
        .saca-title { 
            font-size: 16px; 
            font-weight: bold; 
            text-align: center; 
            margin-bottom: 10px;
            background: #000;
            color: #fff;
            padding: 5px;
        }

        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 8px;
            margin-bottom: 8px;
        }

        .info-item {
            border: 1px solid #000;
            padding: 5px;
            text-align: center;
        }

        .info-label {
            font-size: 10px;
            font-weight: bold;
            display: block;
            margin-bottom: 3px;
        }

        .info-value {
            font-size: 14px;
            font-weight: bold;
        }

        .large-info {
            border: 2px solid #000;
            padding: 10px;
            text-align: center;
            margin-bottom: 8px;
            background: #f9f9f9;
        }

        .large-info .info-label {
            font-size: 11px;
            margin-bottom: 5px;
        }

        .large-info .info-value {
            font-size: 20px;
            font-weight: bold;
        }

        .qr { 
            text-align: center; 
            margin-top: 6px;
            border: 2px solid #000;
            padding: 8px;
        }
        
        .qr img { 
            width: 120px; 
            height: 120px; 
        }

        .footer-info {
            font-size: 9px;
            text-align: center;
            margin-top: 5px;
            color: #666;
        }

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
                <img src="<?= $baseUrl ?>/assets/img/logo-rapi.ico" alt="RAPIEXPRESS Logo">
            </div>
        </div>

        <div class="content">
            <div class="saca-title">📦 SACA</div>

            <!-- Código de Saca destacado -->
            <div class="large-info">
                <span class="info-label">CÓDIGO DE SACA</span>
                <div class="info-value"><?= htmlspecialchars($codigo) ?></div>
            </div>

            <!-- Grid de información -->
            <div class="info-grid">
                <div class="info-item">
                    <span class="info-label">ESTADO</span>
                    <div class="info-value"><?= htmlspecialchars($estado) ?></div>
                </div>
                <div class="info-item">
                    <span class="info-label">PAQUETES</span>
                    <div class="info-value"><?= htmlspecialchars($cantidad) ?></div>
                </div>
            </div>

            <div class="separator"></div>

            <!-- Información detallada -->
            <div class="field">
                <div class="field-label">USUARIO RESPONSABLE:</div>
                <div class="field-value"><?= htmlspecialchars($usuario) ?></div>
            </div>

            <div class="field">
                <div class="field-label">SUCURSAL DESTINO:</div>
                <div class="field-value"><?= htmlspecialchars($sucursal) ?></div>
            </div>

            <div class="field">
                <div class="field-label">PESO TOTAL:</div>
                <div class="field-value"><?= htmlspecialchars($peso) ?> KG</div>
            </div>

            <div class="field">
                <div class="field-label">FECHA DE CREACIÓN:</div>
                <div class="field-value"><?= htmlspecialchars($fecha) ?></div>
            </div>

            <div class="separator"></div>

            <!-- ✅ QR CON URL ABSOLUTA -->
            <div class="qr">
                <?php if (!empty($idSaca)): ?>
                    <img src="<?= $baseUrl ?>/index.php?c=saca&a=generarQR&id=<?= htmlspecialchars($idSaca) ?>&v=<?= $timestamp ?>" 
                         alt="QR Code"
                         onerror="this.parentElement.innerHTML='<p style=\'color:red;\'>Error al cargar QR</p>'">
                <?php else: ?>
                    <p style="color: #999;">[Sin QR]</p>
                <?php endif; ?>
            </div>

            <div class="footer-info">
                RAPIEXPRESS - Sistema de Gestión de Sacas
            </div>
        </div>
    </div>

    <script>
        // ✅ Debug: Mostrar información en consola
        console.log('🔍 Debug Etiqueta Saca:');
        console.log('ID Saca:', <?= json_encode($idSaca) ?>);
        console.log('Base URL:', <?= json_encode($baseUrl) ?>);
        console.log('QR URL:', <?= json_encode($baseUrl . '/index.php?c=saca&a=generarQR&id=' . $idSaca . '&v=' . $timestamp) ?>);
    </script>
</body>
</html>