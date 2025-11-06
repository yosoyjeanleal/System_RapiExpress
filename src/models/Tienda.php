<?php
namespace RapiExpress\Models;

use PDO;
use PDOException;
use RapiExpress\Config\Conexion;

class Tienda extends Conexion 
{
    private function validarNombre(string $nombre): bool {
        return preg_match('/^[a-zA-Z0-9\sáéíóúÁÉÍÓÚñÑ.,-]{3,50}$/', $nombre);
    }

    private function validarDireccion(string $direccion): bool {
        return preg_match('/^[a-zA-Z0-9\sáéíóúÁÉÍÓÚñÑ.,#-]{5,255}$/', $direccion);
    }

    private function validarTelefono(string $telefono): bool {
        return preg_match('/^\+?\d{7,20}$/', $telefono);
    }

    private function validarCorreo(string $correo): bool {
        return filter_var($correo, FILTER_VALIDATE_EMAIL) !== false;
    }

    // =================== REGISTRAR ===================
    public function registrar(array $data): string
    {
        try {
            if (!$this->validarNombre($data['nombre_tienda'])) return 'nombre_invalido';
            if (!$this->validarDireccion($data['direccion_tienda'])) return 'direccion_invalida';
            if (!$this->validarTelefono($data['telefono_tienda'])) return 'telefono_invalido';
            if (!$this->validarCorreo($data['correo_tienda'])) return 'correo_invalido';

            // Verificar duplicados
            $verificaciones = [
                ['campo' => 'Tienda_Nombre', 'valor' => $data['nombre_tienda'], 'error' => 'nombre_existente'],
                ['campo' => 'Tienda_Direccion', 'valor' => $data['direccion_tienda'], 'error' => 'direccion_existente'],
                ['campo' => 'Tienda_Telefono', 'valor' => $data['telefono_tienda'], 'error' => 'telefono_existente'],
                ['campo' => 'Tienda_Correo', 'valor' => $data['correo_tienda'], 'error' => 'correo_existente']
            ];

            foreach ($verificaciones as $v) {
                $stmt = $this->db->prepare("SELECT ID_Tienda FROM tiendas WHERE {$v['campo']} = ?");
                $stmt->execute([$v['valor']]);
                if ($stmt->fetch()) return $v['error'];
            }

            $stmt = $this->db->prepare("INSERT INTO tiendas (Tienda_Nombre, Tienda_Direccion, Tienda_Telefono, Tienda_Correo) VALUES (?, ?, ?, ?)");
            return $stmt->execute([$data['nombre_tienda'], $data['direccion_tienda'], $data['telefono_tienda'], $data['correo_tienda']])
                ? 'registro_exitoso' : 'error_registro';

        } catch (PDOException $e) {
            error_log("Error registrar tienda: ".$e->getMessage());
            return 'error_bd';
        }
    }

    // =================== ACTUALIZAR ===================
    public function actualizar(array $data): string|bool
    {
        try {
            if (!$this->validarNombre($data['nombre_tienda'])) return 'nombre_invalido';
            if (!$this->validarDireccion($data['direccion_tienda'])) return 'direccion_invalida';
            if (!$this->validarTelefono($data['telefono_tienda'])) return 'telefono_invalido';
            if (!$this->validarCorreo($data['correo_tienda'])) return 'correo_invalido';

            // Verificar duplicados excluyendo el ID actual
            $verificaciones = [
                ['campo' => 'Tienda_Nombre', 'valor' => $data['nombre_tienda'], 'error' => 'nombre_existente'],
                ['campo' => 'Tienda_Direccion', 'valor' => $data['direccion_tienda'], 'error' => 'direccion_existente'],
                ['campo' => 'Tienda_Telefono', 'valor' => $data['telefono_tienda'], 'error' => 'telefono_existente'],
                ['campo' => 'Tienda_Correo', 'valor' => $data['correo_tienda'], 'error' => 'correo_existente']
            ];

            foreach ($verificaciones as $v) {
                $stmt = $this->db->prepare("SELECT ID_Tienda FROM tiendas WHERE {$v['campo']} = ? AND ID_Tienda != ?");
                $stmt->execute([$v['valor'], $data['id_tienda']]);
                if ($stmt->fetch()) return $v['error'];
            }

            $stmt = $this->db->prepare("UPDATE tiendas SET Tienda_Nombre = ?, Tienda_Direccion = ?, Tienda_Telefono = ?, Tienda_Correo = ? WHERE ID_Tienda = ?");
            return $stmt->execute([$data['nombre_tienda'], $data['direccion_tienda'], $data['telefono_tienda'], $data['correo_tienda'], $data['id_tienda']])
                ? true : 'error_actualizar';

        } catch (PDOException $e) {
            error_log("Error actualizar tienda: ".$e->getMessage());
            return 'error_bd';
        }
    }

    // =================== ELIMINAR (CORREGIDO) ===================
    public function eliminar(int $id): string|bool
    {
        try {
            // 🔍 Verificar si la tienda está en uso en prealertas
            $stmt = $this->db->prepare("SELECT COUNT(*) FROM prealertas WHERE ID_Tienda = ?");
            $stmt->execute([$id]);
            $count = $stmt->fetchColumn();
            
            if ($count > 0) {
                return "en_uso:No se puede eliminar la tienda porque está asociada a {$count} prealerta(s). Primero debe desvincularla o reasignar esas prealertas.";
            }

            // Si no está en uso, proceder con la eliminación
            $stmt = $this->db->prepare("DELETE FROM tiendas WHERE ID_Tienda = ?");
            $ok = $stmt->execute([$id]);
            
            return $ok ? true : 'error_bd';

        } catch (PDOException $e) {
            error_log("Error eliminar tienda: ".$e->getMessage());
            return 'error_bd';
        }
    }

    // =================== OBTENER ===================
    public function obtenerTodas(): array
    {
        try {
            $stmt = $this->db->prepare("SELECT * FROM tiendas ORDER BY ID_Tienda DESC");
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error obtener tiendas: ".$e->getMessage());
            return [];
        }
    }

    public function obtenerPorId(int $id): ?array
    {
        try {
            $stmt = $this->db->prepare("SELECT * FROM tiendas WHERE ID_Tienda = ?");
            $stmt->execute([$id]);
            return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
        } catch (PDOException $e) {
            error_log("Error obtener tienda por ID: ".$e->getMessage());
            return null;
        }
    }
}