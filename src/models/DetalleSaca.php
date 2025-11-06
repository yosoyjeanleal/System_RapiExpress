<?php
namespace RapiExpress\Models;

use PDO;

use PDOException;
use RapiExpress\Config\Conexion;
use RapiExpress\Interface;
use RapiExpress\Interface\IDetalleSacaModel;

class DetalleSaca extends Conexion  implements IDetalleSacaModel{

public function obtenerPorSaca(int $idSaca): array {
    try {
        $stmt = $this->db->prepare("
            SELECT p.*, 
                   c.Nombres_Cliente, c.Apellidos_Cliente, c.Telefono_Cliente, c.Direccion_Cliente,
                   ct.Categoria_Nombre, 
                   cr.Courier_Nombre, 
                   s.Sucursal_Nombre,
                   p.Nombre_Instrumento
            FROM detalle_sacas ds
            INNER JOIN paquetes p ON ds.ID_Paquete = p.ID_Paquete
            LEFT JOIN clientes c ON p.ID_Cliente = c.ID_Cliente
            LEFT JOIN categorias ct ON p.ID_Categoria = ct.ID_Categoria
            LEFT JOIN courier cr ON p.ID_Courier = cr.ID_Courier
            LEFT JOIN sucursales s ON p.ID_Sucursal = s.ID_Sucursal
            WHERE ds.ID_Saca = :idSaca
        ");
        $stmt->execute([':idSaca' => $idSaca]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Error al obtener paquetes de la saca: " . $e->getMessage());
        return [];
    }
}

public function agregarPaquete($idSaca, $idPaquete): string {
    try {
        // Verificar si el paquete ya está asignado a una saca
        $stmtCheck = $this->db->prepare("SELECT * FROM detalle_sacas WHERE ID_Paquete = ?");
        $stmtCheck->execute([$idPaquete]);
        if ($stmtCheck->fetch()) {
            return 'paquete_ya_asignado';
        }

        // Obtener estado e ID de sucursal del paquete
        $stmtPaquete = $this->db->prepare("
            SELECT Estado, ID_Sucursal 
            FROM paquetes 
            WHERE ID_Paquete = ?
        ");
        $stmtPaquete->execute([$idPaquete]);
        $paquete = $stmtPaquete->fetch(PDO::FETCH_ASSOC);

        if (!$paquete) {
            return 'paquete_no_existe';
        }

        // Verificar que el paquete esté en tránsito
        if ($paquete['Estado'] !== 'En tránsito') {
            return 'paquete_no_apto';
        }

        // Obtener sucursal de la saca
        $stmtSaca = $this->db->prepare("SELECT ID_Sucursal FROM sacas WHERE ID_Saca = ?");
        $stmtSaca->execute([$idSaca]);
        $saca = $stmtSaca->fetch(PDO::FETCH_ASSOC);

        if (!$saca) {
            return 'saca_no_existe';
        }

        // Verificar que sea la misma sucursal
        if ((int)$paquete['ID_Sucursal'] !== (int)$saca['ID_Sucursal']) {
            return 'sucursal_diferente';
        }

        // Insertar en detalle_sacas
        $stmt = $this->db->prepare("
            INSERT INTO detalle_sacas (ID_Saca, ID_Paquete, Fecha_Agregado)
            VALUES (?, ?, NOW())
        ");
        $stmt->execute([$idSaca, $idPaquete]);

        // Actualizar el campo ID_Saca en la tabla paquetes
        $stmtUpdate = $this->db->prepare("
            UPDATE paquetes 
            SET ID_Saca = :idSaca 
            WHERE ID_Paquete = :idPaquete
        ");
        $stmtUpdate->execute([
            ':idSaca' => $idSaca,
            ':idPaquete' => $idPaquete
        ]);

        // Actualizar peso total de la saca
        $this->actualizarPesoSaca($idSaca);

        return 'agregado_exitoso';

    } catch (PDOException $e) {
        error_log("Error al agregar paquete a saca: " . $e->getMessage());
        return 'error_bd';
    }
}


public function quitarPaquete(int $idPaquete, int $idSaca): bool {
    try {
        $stmt = $this->db->prepare("DELETE FROM detalle_sacas WHERE ID_Paquete = :idPaquete");
        $stmt->execute([':idPaquete' => $idPaquete]);

        // ✅ Quitar la referencia en la tabla paquetes
        $stmtUpdate = $this->db->prepare("UPDATE paquetes SET ID_Saca = NULL WHERE ID_Paquete = :idPaquete");
        $stmtUpdate->execute([':idPaquete' => $idPaquete]);

        // Luego actualiza el peso de la saca
        $this->actualizarPesoSaca($idSaca);

        return true;
    } catch (PDOException $e) {
        error_log("Error al quitar paquete de saca: " . $e->getMessage());
        return false;
    }
}



public function actualizarPesoSaca(int $idSaca): bool {
    try {
        // Obtener el peso total actual de los paquetes asociados
        $stmt = $this->db->prepare("
            SELECT SUM(p.Paquete_Peso) AS totalPeso
            FROM detalle_sacas ds
            INNER JOIN paquetes p ON ds.ID_Paquete = p.ID_Paquete
            WHERE ds.ID_Saca = :idSaca
        ");
        $stmt->execute([':idSaca' => $idSaca]);
        $totalPeso = $stmt->fetchColumn();

        // Si no hay paquetes, SUM devuelve NULL → poner 0
        if ($totalPeso === null) {
            $totalPeso = 0;
        }

        // Actualizar la tabla sacas con el peso total calculado
        $stmtUpdate = $this->db->prepare("
            UPDATE sacas SET Peso_Total = :peso WHERE ID_Saca = :idSaca
        ");
        $stmtUpdate->execute([
            ':peso' => $totalPeso,
            ':idSaca' => $idSaca
        ]);

        return true;
    } catch (PDOException $e) {
        error_log("Error al actualizar peso de saca: " . $e->getMessage());
        return false;
    }
}


}
