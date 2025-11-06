<?php
namespace RapiExpress\Models;
use RapiExpress\Config\Conexion;
use PDO;
use RapiExpress\Interface\ISeguimientoModel;



class Seguimiento extends Conexion implements ISeguimientoModel {

    // Obtener seguimientos con info de cliente y paquete
    public function obtenerTodos($filtro = null) {
        $sql = "SELECT s.*, 
                       c.Nombres_Cliente, c.Apellidos_Cliente, 
                       p.Tracking, p.Prealerta_Descripcion
                FROM seguimientos s
                LEFT JOIN clientes c ON s.ID_Cliente = c.ID_Cliente
                LEFT JOIN paquetes p ON s.ID_Paquete = p.ID_Paquete";

        if ($filtro) {
            $sql .= " WHERE p.Tracking LIKE :q OR CONCAT(c.Nombres_Cliente,' ',c.Apellidos_Cliente) LIKE :q";
        }

        $sql .= " ORDER BY s.Fecha DESC";

        $stmt = $this->db->prepare($sql);

        if ($filtro) {
            $stmt->execute([':q' => "%$filtro%"]);
        } else {
            $stmt->execute();
        }

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Registrar seguimiento
    public function registrar($data) {
        $stmt = $this->db->prepare("INSERT INTO seguimientos 
            (ID_Cliente, ID_Paquete, Estado) 
            VALUES (:cliente, :paquete, :estado)");
        return $stmt->execute([
            ':cliente' => $data['ID_Cliente'],
            ':paquete' => $data['ID_Paquete'],
            ':estado'  => $data['Estado']
        ]);
    }
}
