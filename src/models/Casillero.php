<?php
namespace RapiExpress\Models;

use RapiExpress\Config\Conexion;
use RapiExpress\Interface\ICasilleroModel;
use PDO;
use PDOException;

class Casillero extends Conexion implements ICasilleroModel
{
    
    private function validarDatos(array $data): ?string
    {
        if (empty($data['Casillero_Nombre']) || empty($data['Direccion'])) {
            return 'campos_vacios';
        }

        if (!preg_match('/^[A-Za-z0-9\s]{3,50}$/', $data['Casillero_Nombre'])) {
            return 'nombre_invalido';
        }

       
        if (!preg_match('/^[A-Za-z0-9\s\.,#\-]{5,100}$/', $data['Direccion'])) {
            return 'direccion_invalida';
        }

        return null;
    }

  
    public function registrar(array $data)
    {
        try {
            
            $error = $this->validarDatos($data);
            if ($error) {
                return $error;
            }

      
            $stmt = $this->db->prepare("SELECT COUNT(*) FROM casilleros WHERE Casillero_Nombre = :nombre");
            $stmt->execute([':nombre' => $data['Casillero_Nombre']]);
            if ($stmt->fetchColumn() > 0) {
                return 'casillero_existente';
            }

            $sql = "INSERT INTO casilleros (Casillero_Nombre, Direccion) VALUES (:nombre, :direccion)";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                ':nombre' => $data['Casillero_Nombre'],
                ':direccion' => $data['Direccion']
            ]);

            return 'registro_exitoso';
        } catch (PDOException $e) {
            error_log("Error en registrar casillero: " . $e->getMessage());
            return 'error_registro';
        }
    }


    public function obtenerTodos()
    {
        try {
            $stmt = $this->db->query("SELECT * FROM casilleros ORDER BY ID_Casillero DESC");
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error al obtener casilleros: " . $e->getMessage());
            return [];
        }
    }

 
    public function actualizar(array $data)
    {
        try {
            $error = $this->validarDatos($data);
            if ($error) {
                return $error;
            }

            $stmt = $this->db->prepare("SELECT COUNT(*) FROM casilleros WHERE Casillero_Nombre = :nombre AND ID_Casillero != :id");
            $stmt->execute([
                ':nombre' => $data['Casillero_Nombre'],
                ':id' => $data['ID_Casillero']
            ]);
            if ($stmt->fetchColumn() > 0) {
                return 'casillero_existente';
            }

            $sql = "UPDATE casilleros 
                    SET Casillero_Nombre = :nombre, Direccion = :direccion 
                    WHERE ID_Casillero = :id";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                ':nombre' => $data['Casillero_Nombre'],
                ':direccion' => $data['Direccion'],
                ':id' => $data['ID_Casillero']
            ]);

            return $stmt->rowCount() > 0 ? 'actualizacion_exitosa' : 'sin_cambios';
        } catch (PDOException $e) {
            error_log("Error al actualizar casillero: " . $e->getMessage());
            return 'error_actualizacion';
        }
    }

 
    public function eliminar(int $id)
    {
        try {
            
            $stmt = $this->db->prepare("SELECT COUNT(*) FROM clientes WHERE ID_Casillero = :id");
            $stmt->execute([':id' => $id]);
            $usadoEnClientes = $stmt->fetchColumn();

            $stmt = $this->db->prepare("SELECT COUNT(*) FROM prealertas WHERE ID_Casillero = :id");
            $stmt->execute([':id' => $id]);
            $usadoEnPrealertas = $stmt->fetchColumn();

            if ($usadoEnClientes > 0 || $usadoEnPrealertas > 0) {
                return 'casillero_asignado';
            }

            
            $stmt = $this->db->prepare("DELETE FROM casilleros WHERE ID_Casillero = :id");
            $stmt->execute([':id' => $id]);

            return $stmt->rowCount() > 0 ? 'eliminado' : 'no_existente';
        } catch (PDOException $e) {
            error_log("Error al eliminar casillero: " . $e->getMessage());
            return 'error_eliminacion';
        }
    }

    
    public function obtenerPorId(int $id)
    {
        try {
            $stmt = $this->db->prepare("SELECT * FROM casilleros WHERE ID_Casillero = :id");
            $stmt->execute([':id' => $id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error al obtener casillero: " . $e->getMessage());
            return null;
        }
    }
}
