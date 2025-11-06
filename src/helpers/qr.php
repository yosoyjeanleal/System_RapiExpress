<?php
use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Writer\PngWriter;

require_once __DIR__ . '/../../vendor/autoload.php';

/**
 * Verifica si la extensión GD está habilitada
 */
function verificar_gd(): bool
{
    return extension_loaded('gd') && function_exists('imagecreatetruecolor');
}

/**
 * Genera y guarda un QR personalizado
 * 
 * @param string $tipo Tipo de QR: 'paquete' o 'saca'
 * @param array $data  Datos a incluir en el código
 * @param string $ruta Carpeta donde guardar (por defecto 'src/storage/qr/')
 * @return string Ruta completa del archivo generado
 * @throws Exception Si GD no está habilitada o hay error al generar
 */
function generar_qr_code(string $tipo, array $data, string $ruta = 'src/storage/qr/'): string
{
    // Verificar extensión GD
    if (!verificar_gd()) {
        throw new Exception('La extensión GD de PHP no está habilitada. Por favor, habilítala en php.ini y reinicia el servidor.');
    }

    // Crear directorio si no existe
    if (!is_dir($ruta)) {
        if (!mkdir($ruta, 0777, true) && !is_dir($ruta)) {
            throw new Exception("No se pudo crear el directorio: $ruta");
        }
    }

    // Verificar permisos de escritura
    if (!is_writable($ruta)) {
        throw new Exception("El directorio no tiene permisos de escritura: $ruta");
    }

    // Generar contenido según tipo
    if ($tipo === 'saca') {
        $contenido = "📦 SACA\n";
        $contenido .= "Código: {$data['Codigo_Saca']}\n";
        $contenido .= "Usuario: {$data['Usuario']}\n";
        $contenido .= "Sucursal: {$data['Sucursal']}\n";
        $contenido .= "Paquetes: {$data['Cantidad_Paquetes']}\n";
        $contenido .= "Peso total: {$data['Peso_Total']} kg\n";
        $contenido .= "Fecha: {$data['Fecha_Creacion']}\n";
        $identificador = $data['Codigo_Saca'];
    } elseif ($tipo === 'paquete') {
        $contenido = "📦 PAQUETE\n";
        $contenido .= "Tracking: {$data['Tracking']}\n";
        $contenido .= "Cliente: {$data['Cliente']}\n";
        $contenido .= "Instrumento: {$data['Instrumento']}\n";
        $contenido .= "Sucursal: {$data['Sucursal']}\n";
        $contenido .= "Courier: {$data['Courier']}\n";
        $contenido .= "Peso: {$data['Peso']} kg\n";
        $contenido .= "Descripción: {$data['Descripcion']}\n";
        $identificador = $data['Tracking'];
    } else {
        throw new InvalidArgumentException("Tipo de QR no válido: $tipo. Use 'paquete' o 'saca'.");
    }

    // Generar nombre de archivo único
    $filename = strtoupper($tipo) . '-' . preg_replace('/[^A-Za-z0-9\-]/', '', $identificador) . '.png';
    $file = rtrim($ruta, '/') . '/' . $filename;

    try {
        // Construir y generar QR
        $builder = new Builder(
            writer: new PngWriter(),
            data: $contenido,
            size: 300,
            margin: 10
        );
        
        $result = $builder->build();
        $result->saveToFile($file);
        
        // Verificar que el archivo se creó correctamente
        if (!file_exists($file)) {
            throw new Exception("El archivo QR no se pudo crear: $file");
        }
        
        return $file;
        
    } catch (Exception $e) {
        error_log("Error al generar código QR: " . $e->getMessage());
        throw new Exception("Error al generar el código QR: " . $e->getMessage());
    }
}

/**
 * Obtiene información sobre la configuración de GD
 */
function obtener_info_gd(): array
{
    if (!verificar_gd()) {
        return [
            'habilitada' => false,
            'mensaje' => 'La extensión GD no está habilitada'
        ];
    }
    
    $info = gd_info();
    return [
        'habilitada' => true,
        'version' => $info['GD Version'] ?? 'Desconocida',
        'formatos' => [
            'PNG' => $info['PNG Support'] ?? false,
            'JPEG' => $info['JPEG Support'] ?? false,
            'GIF' => $info['GIF Create Support'] ?? false,
        ]
    ];
}