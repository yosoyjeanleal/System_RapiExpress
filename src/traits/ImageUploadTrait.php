<?php
// src/Traits/ImageUploadTrait.php

namespace RapiExpress\Traits;

trait ImageUploadTrait {
    /**
     * Valida un archivo de imagen subido
     * @param array $file - elemento de $_FILES
     * @param int $maxSize - tamaño máximo en bytes (default: 2MB)
     * @param array $allowedMimes - tipos MIME permitidos
     * @return array ['ok' => bool, 'mensaje' => string, 'mime' => string|null]
     */
    public function validateImageFile(
        array $file, 
        int $maxSize = 2_000_000, 
        array $allowedMimes = ['image/jpeg','image/png','image/gif','image/webp']
    ): array {
        // Verificar si hay error en la subida
        if (!isset($file) || $file['error'] !== UPLOAD_ERR_OK) {
            $errorMsg = match ($file['error'] ?? UPLOAD_ERR_NO_FILE) {
                UPLOAD_ERR_INI_SIZE, UPLOAD_ERR_FORM_SIZE => 'El archivo excede el tamaño máximo permitido',
                UPLOAD_ERR_PARTIAL => 'El archivo se subió parcialmente',
                UPLOAD_ERR_NO_FILE => 'No se subió ningún archivo',
                default => 'Error al subir el archivo'
            };
            return ['ok' => false, 'mensaje' => $errorMsg];
        }

        // Verificar tamaño
        if ($file['size'] > $maxSize) {
            $maxMB = round($maxSize / 1_000_000, 1);
            return ['ok' => false, 'mensaje' => "El archivo excede el tamaño máximo de {$maxMB}MB"];
        }

        // Verificar que sea realmente una imagen (evita spoofing)
        if (!getimagesize($file['tmp_name'])) {
            return ['ok' => false, 'mensaje' => 'El archivo no es una imagen válida'];
        }

        // Validación MIME segura usando finfo
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);

        if (!in_array($mime, $allowedMimes)) {
            return ['ok' => false, 'mensaje' => 'Tipo de archivo no permitido. Use JPG, PNG, GIF o WebP'];
        }

        return ['ok' => true, 'mensaje' => 'Archivo válido', 'mime' => $mime];
    }

    /**
     * Guarda el archivo en el servidor con nombre único
     * @param array $file - elemento $_FILES
     * @param string $destFolder - carpeta destino relativa (ej: 'uploads/')
     * @param string|null $prefix - prefijo opcional para el nombre
     * @return array ['ok' => bool, 'mensaje' => string, 'path' => string|null, 'filename' => string|null]
     */
    public function storeImageFile(
        array $file, 
        string $destFolder = 'uploads/', 
        string $prefix = null
    ): array {
        // Crear carpeta si no existe
        if (!is_dir($destFolder)) {
            if (!mkdir($destFolder, 0755, true)) {
                return ['ok' => false, 'mensaje' => 'No se pudo crear la carpeta de destino'];
            }
        }

        // Generar nombre único y seguro
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $token = bin2hex(random_bytes(8));
        $prefix = $prefix ? preg_replace('/[^a-z0-9\-_]/i', '', $prefix) . '_' : '';
        $filename = $prefix . date('Ymd_His') . '_' . $token . '.' . $ext;
        $destination = rtrim($destFolder, '/') . '/' . $filename;

        // Mover archivo
        if (!move_uploaded_file($file['tmp_name'], $destination)) {
            return ['ok' => false, 'mensaje' => 'Error al guardar el archivo'];
        }

        // Establecer permisos seguros
        chmod($destination, 0644);

        return [
            'ok' => true, 
            'mensaje' => 'Archivo guardado correctamente', 
            'path' => $destination, 
            'filename' => $filename
        ];
    }

    /**
     * Elimina un archivo físico del servidor
     * @param string $path - ruta del archivo a eliminar
     * @return bool - true si se eliminó, false si no
     */
    public function deletePhysicalFile(string $path): bool {
        if (empty($path) || $path === 'default.png') {
            return false;
        }

        if (file_exists($path) && is_file($path)) {
            return @unlink($path);
        }

        return false;
    }

    /**
     * Optimiza una imagen (reduce tamaño sin perder calidad visible)
     * @param string $sourcePath - ruta de la imagen original
     * @param int $maxWidth - ancho máximo (default: 1200px)
     * @param int $quality - calidad JPG (default: 85)
     * @return bool
     */
    public function optimizeImage(string $sourcePath, int $maxWidth = 1200, int $quality = 85): bool {
        if (!file_exists($sourcePath)) {
            return false;
        }

        $imageInfo = getimagesize($sourcePath);
        if (!$imageInfo) {
            return false;
        }

        list($width, $height, $type) = $imageInfo;

        // Si es más pequeña que maxWidth, no hacer nada
        if ($width <= $maxWidth) {
            return true;
        }

        // Calcular nuevas dimensiones
        $newWidth = $maxWidth;
        $newHeight = (int) ($height * ($maxWidth / $width));

        // Crear imagen desde el tipo correspondiente
        $source = match($type) {
            IMAGETYPE_JPEG => imagecreatefromjpeg($sourcePath),
            IMAGETYPE_PNG => imagecreatefrompng($sourcePath),
            IMAGETYPE_GIF => imagecreatefromgif($sourcePath),
            IMAGETYPE_WEBP => imagecreatefromwebp($sourcePath),
            default => null
        };

        if (!$source) {
            return false;
        }

        // Crear imagen redimensionada
        $thumb = imagecreatetruecolor($newWidth, $newHeight);

        // Preservar transparencia para PNG y GIF
        if ($type === IMAGETYPE_PNG || $type === IMAGETYPE_GIF) {
            imagealphablending($thumb, false);
            imagesavealpha($thumb, true);
            $transparent = imagecolorallocatealpha($thumb, 255, 255, 255, 127);
            imagefilledrectangle($thumb, 0, 0, $newWidth, $newHeight, $transparent);
        }

        // Redimensionar
        imagecopyresampled($thumb, $source, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);

        // Guardar según el tipo
        $result = match($type) {
            IMAGETYPE_JPEG => imagejpeg($thumb, $sourcePath, $quality),
            IMAGETYPE_PNG => imagepng($thumb, $sourcePath, 9),
            IMAGETYPE_GIF => imagegif($thumb, $sourcePath),
            IMAGETYPE_WEBP => imagewebp($thumb, $sourcePath, $quality),
            default => false
        };

        // Liberar memoria
        imagedestroy($source);
        imagedestroy($thumb);

        return $result;
    }
}