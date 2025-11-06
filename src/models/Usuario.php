<?php
// src/Models/Usuario.php

namespace RapiExpress\Models;

use RapiExpress\Traits\ImageUploadTrait;
use RapiExpress\Config\Conexion;
use PDO;
use PDOException;

class Usuario extends Conexion {
    use ImageUploadTrait;

    private $ID_Usuario;
    private $Cedula_Identidad;
    private $Username;
    private $Nombres_Usuario;
    private $Apellidos_Usuario;
    private $Telefono_Usuario;
    private $Correo_Usuario;
    private $Direccion_Usuario;
    private $ID_Sucursal;
    private $ID_Cargo;
    private $Password;
    private string $lastError = '';

    public function __construct($data = []) {
        parent::__construct();

        $this->ID_Usuario        = $data['ID_Usuario'] ?? null;
        $this->Cedula_Identidad  = $data['Cedula_Identidad'] ?? '';
        $this->Username          = $data['Username'] ?? '';
        $this->Nombres_Usuario   = $data['Nombres_Usuario'] ?? '';
        $this->Apellidos_Usuario = $data['Apellidos_Usuario'] ?? '';
        $this->Telefono_Usuario  = $data['Telefono_Usuario'] ?? '';
        $this->Correo_Usuario    = $data['Correo_Usuario'] ?? '';
        $this->Direccion_Usuario = $data['Direccion_Usuario'] ?? '';
        $this->ID_Sucursal       = $data['ID_Sucursal'] ?? null;
        $this->ID_Cargo          = $data['ID_Cargo'] ?? null;
        $this->Password          = $data['Password'] ?? '';
    }

    // ========== LOGIN ==========
    public function login(string $username, string $password): array {
        try {
            $stmt = $this->db->prepare("SELECT * FROM usuarios WHERE Username = ?");
            $stmt->execute([$username]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$user) {
                return ['estado' => 'error', 'mensaje' => 'Usuario no encontrado'];
            }

            if (!password_verify($password, $user['Password'])) {
                return ['estado' => 'error', 'mensaje' => 'Contraseña incorrecta'];
            }

            unset($user['Password']);
            return ['estado' => 'success', 'usuario' => $user];

        } catch (PDOException $e) {
            error_log("Error login Usuario: " . $e->getMessage());
            return ['estado' => 'error', 'mensaje' => 'Error en el login'];
        }
    }

    // ========== REGISTRAR ==========
    public function registrar(array $data): array {
        try {
            // Verificar duplicados
            $stmt = $this->db->prepare("SELECT * FROM usuarios WHERE Cedula_Identidad = ? OR Correo_Usuario = ? OR Username = ? OR Telefono_Usuario = ?");
            $stmt->execute([
                $data['Cedula_Identidad'],
                $data['Correo_Usuario'],
                $data['Username'],
                $data['Telefono_Usuario']
            ]);
            $existe = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($existe) {
                if ($existe['Cedula_Identidad'] == $data['Cedula_Identidad']) {
                    return ['estado' => 'error', 'mensaje' => 'La cédula ya está registrada'];
                }
                if ($existe['Correo_Usuario'] == $data['Correo_Usuario']) {
                    return ['estado' => 'error', 'mensaje' => 'El correo ya está registrado'];
                }
                if ($existe['Username'] == $data['Username']) {
                    return ['estado' => 'error', 'mensaje' => 'El nombre de usuario ya existe'];
                }
                if ($existe['Telefono_Usuario'] == $data['Telefono_Usuario']) {
                    return ['estado' => 'error', 'mensaje' => 'El teléfono ya está registrado'];
                }
            }

            // Insertar usuario
            $sql = "INSERT INTO usuarios 
                (Cedula_Identidad, Nombres_Usuario, Apellidos_Usuario, Username, Password, 
                 Telefono_Usuario, Correo_Usuario, Direccion_Usuario, ID_Sucursal, ID_Cargo, ID_Imagen, Fecha_Registro)
                VALUES (?,?,?,?,?,?,?,?,?,?,?, NOW())";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                $data['Cedula_Identidad'],
                $data['Nombres_Usuario'],
                $data['Apellidos_Usuario'],
                $data['Username'],
                password_hash($data['Password'], PASSWORD_BCRYPT),
                $data['Telefono_Usuario'] ?? null,
                $data['Correo_Usuario'] ?? null,
                $data['Direccion_Usuario'] ?? null,
                $data['ID_Sucursal'] ?? null,
                $data['ID_Cargo'] ?? null,
                $data['ID_Imagen'] ?? null
            ]);

            return ['estado' => 'success', 'mensaje' => 'Usuario registrado correctamente'];

        } catch (PDOException $e) {
            error_log("Error registrar Usuario: " . $e->getMessage());
            return ['estado' => 'error', 'mensaje' => 'Error al registrar usuario'];
        }
    }

    // ========== ACTUALIZAR (para gestión de usuarios) ==========
    public function actualizar(array $data): array {
        try {
            // Verificar duplicados excluyendo el usuario actual
            $stmt = $this->db->prepare("
                SELECT * FROM usuarios 
                WHERE (Username = ? OR Cedula_Identidad = ? OR Correo_Usuario = ? OR Telefono_Usuario = ?) 
                AND ID_Usuario != ?
            ");
            $stmt->execute([
                $data['Username'],
                $data['Cedula_Identidad'],
                $data['Correo_Usuario'],
                $data['Telefono_Usuario'],
                $data['ID_Usuario']
            ]);
            $existe = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($existe) {
                if ($existe['Cedula_Identidad'] == $data['Cedula_Identidad']) {
                    return ['estado' => 'error', 'mensaje' => 'La cédula ya está registrada'];
                }
                if ($existe['Username'] == $data['Username']) {
                    return ['estado' => 'error', 'mensaje' => 'El nombre de usuario ya existe'];
                }
                if ($existe['Correo_Usuario'] == $data['Correo_Usuario']) {
                    return ['estado' => 'error', 'mensaje' => 'El correo ya está registrado'];
                }
                if ($existe['Telefono_Usuario'] == $data['Telefono_Usuario']) {
                    return ['estado' => 'error', 'mensaje' => 'El teléfono ya está registrado'];
                }
            }

            // Actualizar usuario
            $sql = "UPDATE usuarios SET 
                Cedula_Identidad = ?, Username = ?, Nombres_Usuario = ?, Apellidos_Usuario = ?, 
                Telefono_Usuario = ?, Correo_Usuario = ?, Direccion_Usuario = ?, 
                ID_Sucursal = ?, ID_Cargo = ? 
                WHERE ID_Usuario = ?";

            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                $data['Cedula_Identidad'],
                $data['Username'],
                $data['Nombres_Usuario'],
                $data['Apellidos_Usuario'],
                $data['Telefono_Usuario'],
                $data['Correo_Usuario'],
                $data['Direccion_Usuario'],
                $data['ID_Sucursal'],
                $data['ID_Cargo'],
                $data['ID_Usuario']
            ]);

            return ['estado' => 'success', 'mensaje' => 'Usuario actualizado correctamente'];

        } catch (PDOException $e) {
            error_log("Error actualizar usuario: " . $e->getMessage());
            return ['estado' => 'error', 'mensaje' => 'Error al actualizar usuario'];
        }
    }

// ========== ACTUALIZAR PERFIL (solo datos personales) ==========
public function actualizarPerfil(int $id, array $data): array {
    try {
        // Obtener datos actuales del usuario
        $stmt = $this->db->prepare("SELECT ID_Imagen FROM usuarios WHERE ID_Usuario = ?");
        $stmt->execute([$id]);
        $userActual = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$userActual) {
            return ['estado' => 'error', 'mensaje' => 'Usuario no encontrado'];
        }
        
        $imagenAnterior = $userActual['ID_Imagen'] ?? null;
        $nuevaImagen = $data['ID_Imagen'] ?? null;
        
        // Solo eliminar imagen anterior si:
        // 1. Hay una nueva imagen diferente
        // 2. La imagen anterior existe
        // 3. No es la imagen por defecto
        if ($nuevaImagen && $imagenAnterior && $nuevaImagen != $imagenAnterior) {
            $this->eliminarImagenFisica($imagenAnterior);
        }
        
        // Actualizar perfil (sin cambiar Username, Cédula, Sucursal, Cargo)
        $sql = "UPDATE usuarios SET 
            Nombres_Usuario = ?, 
            Apellidos_Usuario = ?, 
            Telefono_Usuario = ?, 
            Correo_Usuario = ?, 
            Direccion_Usuario = ?, 
            ID_Imagen = ? 
            WHERE ID_Usuario = ?";
        
        $stmt = $this->db->prepare($sql);
        $result = $stmt->execute([
            $data['Nombres_Usuario'],
            $data['Apellidos_Usuario'],
            $data['Telefono_Usuario'] ?? null,
            $data['Correo_Usuario'] ?? null,
            $data['Direccion_Usuario'] ?? null,
            $nuevaImagen, // Puede ser NULL
            $id
        ]);
        
        if (!$result) {
            return ['estado' => 'error', 'mensaje' => 'Error al actualizar en la base de datos'];
        }
        
        return ['estado' => 'success', 'mensaje' => 'Perfil actualizado correctamente'];

    } catch(PDOException $e) {
        error_log("Error actualizarPerfil Usuario: " . $e->getMessage());
        return ['estado' => 'error', 'mensaje' => 'Error en la base de datos: ' . $e->getMessage()];
    }
}

// ========== SUBIR IMAGEN DE PERFIL ==========
public function subirImagenPerfil(array $file): array {
    // Validar archivo
    $valid = $this->validateImageFile($file, 5_000_000);
    if (!$valid['ok']) {
        return ['estado' => 'error', 'mensaje' => $valid['mensaje']];
    }

    // Guardar archivo físico
    $stored = $this->storeImageFile($file, 'uploads/', 'perfil');
    if (!$stored['ok']) {
        return ['estado' => 'error', 'mensaje' => $stored['mensaje']];
    }

    // Registrar en BD
    try {
        $stmt = $this->db->prepare("INSERT INTO imagenes (imagen_nombre_original, imagen_archivo) VALUES (?,?)");
        $result = $stmt->execute([$file['name'], $stored['filename']]);
        
        if (!$result) {
            // Si falla BD, eliminar archivo físico
            @unlink($stored['path']);
            return ['estado' => 'error', 'mensaje' => 'Error al registrar imagen en la base de datos'];
        }
        
        $idImagen = $this->db->lastInsertId();
        
        return [
            'estado' => 'success',
            'mensaje' => 'Imagen subida correctamente',
            'ID_Imagen' => $idImagen,
            'path' => $stored['path'],
            'filename' => $stored['filename']
        ];
    } catch(PDOException $e) {
        // Si falla BD, eliminar archivo físico
        @unlink($stored['path']);
        error_log("Error registrar imagen perfil: " . $e->getMessage());
        return ['estado' => 'error', 'mensaje' => 'Error en la base de datos: ' . $e->getMessage()];
    }
}

private function eliminarImagenFisica(int $idImagen): void {
    try {
        $stmt = $this->db->prepare("SELECT imagen_archivo FROM imagenes WHERE ID_Imagen = ?");
        $stmt->execute([$idImagen]);
        $img = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($img && $img['imagen_archivo'] && $img['imagen_archivo'] !== 'default.png') {
            $path = 'uploads/' . $img['imagen_archivo'];
            $this->deletePhysicalFile($path);
        }
        
        // Eliminar registro de BD
        $stmt = $this->db->prepare("DELETE FROM imagenes WHERE ID_Imagen = ?");
        $stmt->execute([$idImagen]);

    } catch(PDOException $e) {
        error_log("Error eliminarImagenFisica: " . $e->getMessage());
    }
}

    // ========== ELIMINAR ==========
// ========== VERIFICAR SI TIENE DEPENDENCIAS ==========
public function tieneDependencias(int $id): array {
    try {
        $dependencias = [];
        
        // Verificar paquetes
        $stmt = $this->db->prepare("SELECT COUNT(*) as total FROM paquetes WHERE ID_Usuario = ?");
        $stmt->execute([$id]);
        $paquetes = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
        if ($paquetes > 0) {
            $dependencias[] = "$paquetes paquete(s)";
        }
        
        // Verificar prealertas
        $stmt = $this->db->prepare("SELECT COUNT(*) as total FROM prealertas WHERE ID_Usuario = ?");
        $stmt->execute([$id]);
        $prealertas = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
        if ($prealertas > 0) {
            $dependencias[] = "$prealertas prealerta(s)";
        }
        
        // Verificar sacas
        $stmt = $this->db->prepare("SELECT COUNT(*) as total FROM sacas WHERE ID_Usuario = ?");
        $stmt->execute([$id]);
        $sacas = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
        if ($sacas > 0) {
            $dependencias[] = "$sacas saca(s)";
        }
        
        // Verificar manifiestos
        $stmt = $this->db->prepare("SELECT COUNT(*) as total FROM manifiestos WHERE ID_Usuario = ?");
        $stmt->execute([$id]);
        $manifiestos = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
        if ($manifiestos > 0) {
            $dependencias[] = "$manifiestos manifiesto(s)";
        }
        
        return [
            'tiene' => !empty($dependencias),
            'mensaje' => !empty($dependencias) 
                ? 'Este usuario está asociado a: ' . implode(', ', $dependencias)
                : ''
        ];
        
    } catch (PDOException $e) {
        error_log("Error tieneDependencias: " . $e->getMessage());
        return ['tiene' => true, 'mensaje' => 'Error al verificar dependencias'];
    }
}

// ========== ELIMINAR ==========
public function eliminar(int $id): array {
    try {
        // ✅ VALIDAR DEPENDENCIAS
        $dependencias = $this->tieneDependencias($id);
        if ($dependencias['tiene']) {
            return [
                'estado' => 'error', 
                'mensaje' => $dependencias['mensaje'] . '. No se puede eliminar.'
            ];
        }
        
        // Obtener imagen del usuario antes de eliminar
        $stmt = $this->db->prepare("SELECT ID_Imagen FROM usuarios WHERE ID_Usuario = ?");
        $stmt->execute([$id]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        // Eliminar usuario
        $stmt = $this->db->prepare("DELETE FROM usuarios WHERE ID_Usuario = ?");
        $stmt->execute([$id]);
        
        // Si tenía imagen, eliminarla
        if ($user && $user['ID_Imagen']) {
            $this->eliminarImagenFisica($user['ID_Imagen']);
        }
        
        return ['estado' => 'success', 'mensaje' => 'Usuario eliminado correctamente'];

    } catch (PDOException $e) {
        error_log("Error eliminar Usuario: " . $e->getMessage());
        return ['estado' => 'error', 'mensaje' => 'Error al eliminar usuario'];
    }
}
    // ========== OBTENER TODOS ==========
    public function obtenerTodos(): array {
        try {
            $sql = "SELECT 
                    u.ID_Usuario,
                    u.Cedula_Identidad,
                    u.Username,
                    u.Nombres_Usuario,
                    u.Apellidos_Usuario,
                    u.Telefono_Usuario,
                    u.Correo_Usuario,
                    u.Direccion_Usuario,
                    u.Fecha_Registro,
                    s.Sucursal_Nombre,
                    c.Cargo_Nombre,
                    i.imagen_archivo
                FROM usuarios u
                LEFT JOIN sucursales s ON u.ID_Sucursal = s.ID_Sucursal
                LEFT JOIN cargos c ON u.ID_Cargo = c.ID_Cargo
                LEFT JOIN imagenes i ON u.ID_Imagen = i.ID_Imagen
                ORDER BY u.Fecha_Registro DESC";
            
            $stmt = $this->db->query($sql);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);

        } catch (PDOException $e) {
            error_log("Error obtenerTodos Usuarios: " . $e->getMessage());
            return [];
        }
    }

    // ========== OBTENER POR ID ==========
    public function obtenerPorId(int $id): ?array {
    try {
        $stmt = $this->db->prepare("
            SELECT 
                u.*, 
                i.imagen_archivo,
                s.Sucursal_Nombre,
                c.Cargo_Nombre
            FROM usuarios u
            LEFT JOIN imagenes i ON u.ID_Imagen = i.ID_Imagen
            LEFT JOIN sucursales s ON u.ID_Sucursal = s.ID_Sucursal
            LEFT JOIN cargos c ON u.ID_Cargo = c.ID_Cargo
            WHERE u.ID_Usuario = ?
        ");
        $stmt->execute([$id]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($user && empty($user['imagen_archivo'])) {
            $user['imagen_archivo'] = 'default.png';
        }
        
        return $user ?: null;

    } catch(PDOException $e) {
        error_log("Error obtenerPorId Usuario: " . $e->getMessage());
        return null;
    }
}

    // ========== GESTIÓN DE IMÁGENES ==========
    
    public function obtenerTodasImagenes(): array {
        try {
            $stmt = $this->db->query("SELECT * FROM imagenes ORDER BY ID_Imagen DESC");
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            error_log("Error obtenerTodasImagenes: " . $e->getMessage());
            return [];
        }
    }
public function obtenerNombreArchivoPorIdImagen($idImagen) {
    if (!$idImagen) return null;
    $stmt = $this->db->prepare("SELECT imagen_archivo FROM imagenes WHERE ID_Imagen = ?");
    $stmt->execute([$idImagen]);
    $row = $stmt->fetch(\PDO::FETCH_ASSOC);
    return $row['imagen_archivo'] ?? null;
}

    // ========== GETTERS ==========
    public function getNombreCompleto(): string {
        return $this->Nombres_Usuario . ' ' . $this->Apellidos_Usuario;
    }


}
