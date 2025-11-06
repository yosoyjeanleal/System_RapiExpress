<?php
namespace RapiExpress\Models;

use PDO;
use PDOException;
use RapiExpress\Config\Conexion;
use RapiExpress\Interface\IClienteModel;

class Cliente extends Conexion implements IClienteModel {

    /** ===========================================
     *  VALIDACIONES INTERNAS
     *  =========================================== */
    
    private function validarCedula(string $cedula): bool {
        return preg_match('/^\d{6,23}$/', $cedula);
    }

    private function validarNombre(string $nombre): bool {
        return preg_match('/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]{2,20}$/', $nombre);
    }

    private function validarApellido(string $apellido): bool {
        return preg_match('/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]{2,20}$/', $apellido);
    }

    private function validarTelefono(string $telefono): bool {
        return preg_match('/^\d{7,15}$/', $telefono);
    }

    private function validarCorreo(string $correo): bool {
        return filter_var($correo, FILTER_VALIDATE_EMAIL) !== false;
    }

    private function validarDireccion(string $direccion): bool {
        return strlen($direccion) >= 5 && strlen($direccion) <= 255;
    }

    /** ===========================================
     *  FUNCIONES CRUD
     *  =========================================== */

    public function registrar(array $data) {
        // Validaciones
        if (!$this->validarCedula($data['Cedula_Identidad'])) return 'cedula_invalida';
        if (!$this->validarNombre($data['Nombres_Cliente'])) return 'nombre_invalido';
        if (!$this->validarApellido($data['Apellidos_Cliente'])) return 'apellido_invalido';
        if (!$this->validarDireccion($data['Direccion_Cliente'])) return 'direccion_invalida';
        if (!$this->validarTelefono($data['Telefono_Cliente'])) return 'telefono_invalido';
        if (!$this->validarCorreo($data['Correo_Cliente'])) return 'correo_invalido';

        try {
            // Verificar duplicados
            $stmt = $this->db->prepare("SELECT ID_Cliente FROM clientes WHERE Cedula_Identidad = ?");
            $stmt->execute([$data['Cedula_Identidad']]);
            if ($stmt->fetch()) return 'cedula_existente';

            $stmt = $this->db->prepare("SELECT ID_Cliente FROM clientes WHERE Telefono_Cliente = ?");
            $stmt->execute([$data['Telefono_Cliente']]);
            if ($stmt->fetch()) return 'telefono_existente';

            $stmt = $this->db->prepare("SELECT ID_Cliente FROM clientes WHERE Correo_Cliente = ?");
            $stmt->execute([$data['Correo_Cliente']]);
            if ($stmt->fetch()) return 'correo_existente';

            // Insertar
            $stmt = $this->db->prepare("INSERT INTO clientes 
                (ID_Cliente, Cedula_Identidad, Nombres_Cliente, Apellidos_Cliente, Direccion_Cliente, Telefono_Cliente, Correo_Cliente, Fecha_Registro, ID_Sucursal, ID_Casillero) 
                VALUES (?, ?, ?, ?, ?, ?, ?, NOW(), ?, ?)");

            return $stmt->execute([
                $data['ID_Cliente'],
                $data['Cedula_Identidad'],
                $data['Nombres_Cliente'],
                $data['Apellidos_Cliente'],
                $data['Direccion_Cliente'],
                $data['Telefono_Cliente'],
                $data['Correo_Cliente'],
                $data['ID_Sucursal'],
                $data['ID_Casillero']
            ]) ? 'registro_exitoso' : 'error_registro';

        } catch (PDOException $e) {
            error_log("Error en registro cliente: " . $e->getMessage());
            return 'error_bd';
        }
    }

    public function actualizar(array $data) {
        // Validaciones
        if (!$this->validarCedula($data['Cedula_Identidad'])) return 'cedula_invalida';
        if (!$this->validarNombre($data['Nombres_Cliente'])) return 'nombre_invalido';
        if (!$this->validarApellido($data['Apellidos_Cliente'])) return 'apellido_invalido';
        if (!$this->validarDireccion($data['Direccion_Cliente'])) return 'direccion_invalida';
        if (!$this->validarTelefono($data['Telefono_Cliente'])) return 'telefono_invalido';
        if (!$this->validarCorreo($data['Correo_Cliente'])) return 'correo_invalido';

        try {
            // Verificar duplicados
            $stmt = $this->db->prepare("SELECT ID_Cliente FROM clientes WHERE Cedula_Identidad = ? AND ID_Cliente != ?");
            $stmt->execute([$data['Cedula_Identidad'], $data['ID_Cliente']]);
            if ($stmt->fetch()) return 'cedula_existente';

            $stmt = $this->db->prepare("SELECT ID_Cliente FROM clientes WHERE Telefono_Cliente = ? AND ID_Cliente != ?");
            $stmt->execute([$data['Telefono_Cliente'], $data['ID_Cliente']]);
            if ($stmt->fetch()) return 'telefono_existente';

            $stmt = $this->db->prepare("SELECT ID_Cliente FROM clientes WHERE Correo_Cliente = ? AND ID_Cliente != ?");
            $stmt->execute([$data['Correo_Cliente'], $data['ID_Cliente']]);
            if ($stmt->fetch()) return 'correo_existente';

            // Actualizar
            $stmt = $this->db->prepare("UPDATE clientes SET 
                Cedula_Identidad = ?, 
                Nombres_Cliente = ?, 
                Apellidos_Cliente = ?, 
                Direccion_Cliente = ?, 
                Telefono_Cliente = ?, 
                Correo_Cliente = ?, 
                ID_Sucursal = ?, 
                ID_Casillero = ?
                WHERE ID_Cliente = ?");

            return $stmt->execute([
                $data['Cedula_Identidad'],
                $data['Nombres_Cliente'],
                $data['Apellidos_Cliente'],
                $data['Direccion_Cliente'],
                $data['Telefono_Cliente'],
                $data['Correo_Cliente'],
                $data['ID_Sucursal'],
                $data['ID_Casillero'],
                $data['ID_Cliente']
            ]) ? 'actualizacion_exitosa' : 'error_actualizacion';

        } catch (PDOException $e) {
            error_log("Error al actualizar cliente: " . $e->getMessage());
            return 'error_bd';
        }
    }

 public function eliminar($id) {
    try {
        // Verificar si existe en paquetes
        $stmt = $this->db->prepare("SELECT ID_Paquete FROM paquetes WHERE ID_Cliente = ?");
        $stmt->execute([$id]);
        if($stmt->fetch()) return 'cliente_relacionado_paquete';

        // Verificar si existe en prealertas
        $stmt = $this->db->prepare("SELECT ID_Prealerta FROM prealertas WHERE ID_Cliente = ?");
        $stmt->execute([$id]);
        if($stmt->fetch()) return 'cliente_relacionado_prealerta';

        // Verificar si existe en seguimientos
        $stmt = $this->db->prepare("SELECT ID_Seguimiento FROM seguimientos WHERE ID_Cliente = ?");
        $stmt->execute([$id]);
        if($stmt->fetch()) return 'cliente_relacionado_seguimiento';

        // Si no tiene relaciones, eliminar
        $stmt = $this->db->prepare("DELETE FROM clientes WHERE ID_Cliente = ?");
        return $stmt->execute([$id]) ? 'eliminacion_exitosa' : 'error_eliminacion';

    } catch (PDOException $e) {
        error_log("Error al eliminar cliente: " . $e->getMessage());
        return 'error_bd';
    }
}


    public function obtenerTodos() {
        try {
            $stmt = $this->db->prepare("
                SELECT 
                    c.ID_Cliente,
                    c.Cedula_Identidad,
                    c.Nombres_Cliente,
                    c.Apellidos_Cliente,
                    c.Direccion_Cliente,
                    c.Telefono_Cliente,
                    c.Correo_Cliente,
                    c.Fecha_Registro,
                    s.Sucursal_Nombre,
                    ca.Casillero_Nombre
                FROM clientes c
                LEFT JOIN sucursales s ON c.ID_Sucursal = s.ID_Sucursal
                LEFT JOIN casilleros ca ON c.ID_Casillero = ca.ID_Casillero
                ORDER BY c.Fecha_Registro DESC
            ");
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error al obtener clientes: " . $e->getMessage());
            return [];
        }
    }

    public function obtenerPorId($id) {
        try {
            $stmt = $this->db->prepare("SELECT * FROM clientes WHERE ID_Cliente = ?");
            $stmt->execute([$id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error al obtener cliente por ID: " . $e->getMessage());
            return null;
        }
    }
}
