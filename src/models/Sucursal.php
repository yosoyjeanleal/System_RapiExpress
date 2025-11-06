<?php
namespace RapiExpress\Models;

use PDO;
use PDOException;
use RapiExpress\Config\Conexion;
use RapiExpress\Interface\ISucursalModel;

class Sucursal extends Conexion implements ISucursalModel
{
    private string $lastError = '';

    // ============================================================
    // 🔹 VALIDACIONES INTERNAS
    // ============================================================
    private function validarRIF(string $rif): bool {
        return preg_match('/^[JGVEP]-\d{8}-\d$/', $rif);
    }

    private function validarNombre(string $nombre): bool {
        return preg_match('/^[a-zA-Z0-9\sáéíóúÁÉÍÓÚñÑ.,\-()_]{3,50}$/', $nombre);
    }

    private function validarDireccion(string $direccion): bool {
        return strlen($direccion) >= 5 && strlen($direccion) <= 100;
    }

    private function validarTelefono(string $telefono): bool {
        return preg_match('/^\d{7,20}$/', $telefono);
    }

    private function validarCorreo(string $correo): bool {
        return filter_var($correo, FILTER_VALIDATE_EMAIL) && strlen($correo) <= 100;
    }

    // ============================================================
    // ✅ REGISTRAR SUCURSAL
    // ============================================================
    public function registrar(array $data): string
    {
        try {
            // 🔍 Validaciones de formato
            if (!$this->validarRIF($data['RIF_Sucursal'])) return 'rif_invalido';
            if (!$this->validarNombre($data['Sucursal_Nombre'])) return 'nombre_invalido';
            if (!$this->validarDireccion($data['Sucursal_Direccion'])) return 'direccion_invalida';
            if (!$this->validarTelefono($data['Sucursal_Telefono'])) return 'telefono_invalido';
            if (!$this->validarCorreo($data['Sucursal_Correo'])) return 'correo_invalido';

            // 🔍 Verificar duplicados
            $verificaciones = [
                ['campo' => 'RIF_Sucursal',      'valor' => $data['RIF_Sucursal'],      'error' => 'rif_existente'],
                ['campo' => 'Sucursal_Nombre',   'valor' => $data['Sucursal_Nombre'],   'error' => 'nombre_existente'],
                ['campo' => 'Sucursal_Telefono', 'valor' => $data['Sucursal_Telefono'], 'error' => 'telefono_existente'],
                ['campo' => 'Sucursal_Correo',   'valor' => $data['Sucursal_Correo'],   'error' => 'correo_existente'],
            ];

            foreach ($verificaciones as $v) {
                $stmt = $this->db->prepare("SELECT 1 FROM sucursales WHERE {$v['campo']} = ?");
                $stmt->execute([$v['valor']]);
                if ($stmt->fetch()) return $v['error'];
            }

            // 🧩 Insertar registro
            $stmt = $this->db->prepare("
                INSERT INTO sucursales (RIF_Sucursal, Sucursal_Nombre, Sucursal_Direccion, Sucursal_Telefono, Sucursal_Correo)
                VALUES (?, ?, ?, ?, ?)
            ");
            $ok = $stmt->execute([
                $data['RIF_Sucursal'],
                $data['Sucursal_Nombre'],
                $data['Sucursal_Direccion'],
                $data['Sucursal_Telefono'],
                $data['Sucursal_Correo']
            ]);

            return $ok ? 'registro_exitoso' : 'error_registro';
        } catch (PDOException $e) {
            $this->lastError = $e->getMessage();
            error_log("❌ Error en registrar sucursal: " . $e->getMessage());
            return 'error_bd';
        }
    }

    // ============================================================
    // ✅ OBTENER TODAS LAS SUCURSALES
    // ============================================================
    public function obtenerTodas(): array
    {
        try {
            $stmt = $this->db->query("SELECT * FROM sucursales ORDER BY ID_Sucursal DESC");
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            $this->lastError = $e->getMessage();
            error_log("❌ Error al obtener sucursales: " . $e->getMessage());
            return [];
        }
    }

    // ============================================================
    // ✅ OBTENER SUCURSAL POR ID
    // ============================================================
    public function obtenerPorId(int $id): ?array
    {
        try {
            $stmt = $this->db->prepare("SELECT * FROM sucursales WHERE ID_Sucursal = ?");
            $stmt->execute([$id]);
            return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
        } catch (PDOException $e) {
            $this->lastError = $e->getMessage();
            error_log("❌ Error al obtener sucursal por ID: " . $e->getMessage());
            return null;
        }
    }

    // ============================================================
    // ✅ ACTUALIZAR SUCURSAL
    // ============================================================
    public function actualizar(array $data): string
    {
        try {
            // 🔍 Validaciones de formato
            if (!$this->validarRIF($data['RIF_Sucursal'])) return 'rif_invalido';
            if (!$this->validarNombre($data['Sucursal_Nombre'])) return 'nombre_invalido';
            if (!$this->validarDireccion($data['Sucursal_Direccion'])) return 'direccion_invalida';
            if (!$this->validarTelefono($data['Sucursal_Telefono'])) return 'telefono_invalido';
            if (!$this->validarCorreo($data['Sucursal_Correo'])) return 'correo_invalido';

            // 🔍 Verificar duplicados
            $verificaciones = [
                ['campo' => 'RIF_Sucursal',      'valor' => $data['RIF_Sucursal'],      'error' => 'rif_existente'],
                ['campo' => 'Sucursal_Nombre',   'valor' => $data['Sucursal_Nombre'],   'error' => 'nombre_existente'],
                ['campo' => 'Sucursal_Telefono', 'valor' => $data['Sucursal_Telefono'], 'error' => 'telefono_existente'],
                ['campo' => 'Sucursal_Correo',   'valor' => $data['Sucursal_Correo'],   'error' => 'correo_existente'],
            ];

            foreach ($verificaciones as $v) {
                $stmt = $this->db->prepare("
                    SELECT ID_Sucursal FROM sucursales
                    WHERE {$v['campo']} = ? AND ID_Sucursal != ?
                ");
                $stmt->execute([$v['valor'], $data['ID_Sucursal']]);
                if ($stmt->fetch()) return $v['error'];
            }

            // 🧩 Actualizar registro
            $stmt = $this->db->prepare("
                UPDATE sucursales SET 
                    RIF_Sucursal = ?, 
                    Sucursal_Nombre = ?, 
                    Sucursal_Direccion = ?, 
                    Sucursal_Telefono = ?, 
                    Sucursal_Correo = ?
                WHERE ID_Sucursal = ?
            ");

            $ok = $stmt->execute([
                $data['RIF_Sucursal'],
                $data['Sucursal_Nombre'],
                $data['Sucursal_Direccion'],
                $data['Sucursal_Telefono'],
                $data['Sucursal_Correo'],
                $data['ID_Sucursal']
            ]);

            return $ok ? 'actualizacion_exitosa' : 'error_actualizacion';
        } catch (PDOException $e) {
            $this->lastError = $e->getMessage();
            error_log("❌ Error al actualizar sucursal: " . $e->getMessage());
            return 'error_bd';
        }
    }

    // ============================================================
    // ✅ ELIMINAR SUCURSAL
    // ============================================================
  // ============================================================
// ✅ ELIMINAR SUCURSAL
// ============================================================
public function eliminar(int $id): string
{
    try {
        $tablas = [
            'clientes' => 'clientes',
            'paquetes' => 'paquetes',
            'prealertas' => 'prealertas',
            'sacas' => 'sacas',
            'usuarios' => 'usuarios'
        ];

        $tablasEnUso = [];
        
        foreach ($tablas as $nombreTabla => $label) {
            $stmt = $this->db->prepare("SELECT COUNT(*) FROM {$nombreTabla} WHERE ID_Sucursal = ?");
            $stmt->execute([$id]);
            $count = $stmt->fetchColumn();
            
            if ($count > 0) {
                $tablasEnUso[] = "{$label} ({$count})";
            }
        }

        if (!empty($tablasEnUso)) {
            // Retornar un JSON con los detalles
            return json_encode([
                'codigo' => 'sucursal_en_uso',
                'tablas' => $tablasEnUso
            ]);
        }

        $stmt = $this->db->prepare("DELETE FROM sucursales WHERE ID_Sucursal = ?");
        $stmt->execute([$id]);
        
        return $stmt->rowCount() > 0 ? 'eliminado' : 'no_existe';
        
    } catch (PDOException $e) {
        $this->lastError = $e->getMessage();
        error_log("❌ Error al eliminar sucursal: " . $e->getMessage());
        return 'error_bd';
    }
}
    // ============================================================
    // 🔹 OBTENER ÚLTIMO ERROR
    // ============================================================
    public function getLastError(): string
    {
        return $this->lastError;
    }
}
