<?php
namespace RapiExpress\Models;

use PDOException;
use RapiExpress\Config\Conexion;
use RapiExpress\Interface\ICourierModel;

class Courier extends Conexion implements ICourierModel {

    // =================== VALIDACIONES ===================

    private function validarRIF(string $rif): bool {
        // Formato: J-12345678-9
        return preg_match('/^[JGVEP]{1}-\d{8}-\d{1}$/', $rif);
    }

    private function validarNombre(string $nombre): bool {
        // Solo letras y espacios, máx. 50 caracteres
        return preg_match('/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]{3,50}$/', $nombre);
    }

    private function validarDireccion(string $direccion): bool {
        // Permitir letras, números y ciertos símbolos
        return preg_match('/^[a-zA-Z0-9\sáéíóúÁÉÍÓÚñÑ.,\-()_#]{5,150}$/', $direccion);
    }

    private function validarTelefono(string $telefono): bool {
        // Ejemplo: 04121234567 o +584121234567
        return preg_match('/^(\+?\d{1,3})?\d{7,15}$/', $telefono);
    }

    private function validarCorreo(string $correo): bool {
        // Validar formato de correo electrónico
        return filter_var($correo, FILTER_VALIDATE_EMAIL);
    }

    private function existeDuplicado(array $data, $excludeId = null) {
        $sql = "SELECT * FROM courier WHERE (RIF_Courier = ? OR Courier_Correo = ? OR Courier_Telefono = ?)";
        if ($excludeId) $sql .= " AND ID_Courier != ?";
        $stmt = $this->db->prepare($sql);

        $params = [
            $data['RIF_Courier'],
            $data['Courier_Correo'],
            $data['Courier_Telefono']
        ];
        if ($excludeId) $params[] = $excludeId;

        $stmt->execute($params);
        return $stmt->fetch();
    }

    // =================== CRUD ===================

    public function registrar(array $data) {
        try {
            // Validaciones de formato
            if (!$this->validarRIF($data['RIF_Courier'])) return 'rif_invalido';
            if (!$this->validarNombre($data['Courier_Nombre'])) return 'nombre_invalido';
            if (!$this->validarDireccion($data['Courier_Direccion'])) return 'direccion_invalida';
            if (!$this->validarTelefono($data['Courier_Telefono'])) return 'telefono_invalido';
            if (!$this->validarCorreo($data['Courier_Correo'])) return 'correo_invalido';

            // Verificar duplicados
            $duplicado = $this->existeDuplicado($data);
            if ($duplicado) {
                if ($duplicado['RIF_Courier'] === $data['RIF_Courier']) return 'rif_duplicado';
                if ($duplicado['Courier_Correo'] === $data['Courier_Correo']) return 'correo_duplicado';
                if ($duplicado['Courier_Telefono'] === $data['Courier_Telefono']) return 'telefono_duplicado';
            }

            // Insertar registro
            $stmt = $this->db->prepare("
                INSERT INTO courier (RIF_Courier, Courier_Nombre, Courier_Direccion, Courier_Telefono, Courier_Correo)
                VALUES (?, ?, ?, ?, ?)
            ");
            $stmt->execute([
                $data['RIF_Courier'],
                $data['Courier_Nombre'],
                $data['Courier_Direccion'],
                $data['Courier_Telefono'],
                $data['Courier_Correo']
            ]);

            return 'registro_exitoso';

        } catch (PDOException $e) {
            error_log("Error en registro de courier: " . $e->getMessage());
            return 'error_bd';
        }
    }

    public function actualizar(array $data) {
        try {
            // Validaciones de formato
            if (!$this->validarRIF($data['RIF_Courier'])) return 'rif_invalido';
            if (!$this->validarNombre($data['Courier_Nombre'])) return 'nombre_invalido';
            if (!$this->validarDireccion($data['Courier_Direccion'])) return 'direccion_invalida';
            if (!$this->validarTelefono($data['Courier_Telefono'])) return 'telefono_invalido';
            if (!$this->validarCorreo($data['Courier_Correo'])) return 'correo_invalido';

            // Verificar duplicados (excluyendo su propio ID)
            $duplicado = $this->existeDuplicado($data, $data['ID_Courier']);
            if ($duplicado) {
                if ($duplicado['RIF_Courier'] === $data['RIF_Courier']) return 'rif_duplicado';
                if ($duplicado['Courier_Correo'] === $data['Courier_Correo']) return 'correo_duplicado';
                if ($duplicado['Courier_Telefono'] === $data['Courier_Telefono']) return 'telefono_duplicado';
            }

            // Actualizar registro
            $stmt = $this->db->prepare("
                UPDATE courier 
                SET RIF_Courier = ?, Courier_Nombre = ?, Courier_Direccion = ?, Courier_Telefono = ?, Courier_Correo = ?
                WHERE ID_Courier = ?
            ");
            $stmt->execute([
                $data['RIF_Courier'],
                $data['Courier_Nombre'],
                $data['Courier_Direccion'],
                $data['Courier_Telefono'],
                $data['Courier_Correo'],
                $data['ID_Courier']
            ]);

            return 'registro_exitoso';

        } catch (PDOException $e) {
            error_log("Error al actualizar courier: " . $e->getMessage());
            return 'error_bd';
        }
    }

    public function eliminar($id) {
        try {
            // Evitar eliminar si tiene paquetes asociados
            $stmtCheck = $this->db->prepare("SELECT COUNT(*) AS total FROM paquetes WHERE ID_Courier = ?");
            $stmtCheck->execute([$id]);
            if ($stmtCheck->fetch()['total'] > 0) {
                return 'error_restriccion';
            }

            $stmt = $this->db->prepare("DELETE FROM courier WHERE ID_Courier = ?");
            $stmt->execute([$id]);
            return $stmt->rowCount() > 0;

        } catch (PDOException $e) {
            error_log("Error al eliminar courier: " . $e->getMessage());
            return false;
        }
    }

    public function obtenerTodos() {
        try {
            $stmt = $this->db->query("SELECT * FROM courier ORDER BY ID_Courier DESC");
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error al obtener couriers: " . $e->getMessage());
            return [];
        }
    }

    public function obtenerPorId($id) {
        try {
            $stmt = $this->db->prepare("SELECT * FROM courier WHERE ID_Courier = ?");
            $stmt->execute([$id]);
            return $stmt->fetch(\PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error al obtener courier por ID: " . $e->getMessage());
            return false;
        }
    }
}
