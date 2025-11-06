<?php
namespace RapiExpress\Models;

use PDOException;
use RapiExpress\Config\Conexion;

class Saca extends Conexion {

public function obtenerTodas() {
    try {
        $stmt = $this->db->prepare("
            SELECT 
                s.ID_Saca,
                s.Codigo_Saca,
                s.ID_Usuario,
                s.ID_Sucursal,
                s.Estado,
                s.Peso_Total,
                u.Nombres_Usuario,
                u.Apellidos_Usuario,
                su.Sucursal_Nombre
            FROM sacas s
            LEFT JOIN usuarios u ON s.ID_Usuario = u.ID_Usuario
            LEFT JOIN sucursales su ON s.ID_Sucursal = su.ID_Sucursal
            ORDER BY s.ID_Saca DESC
        ");
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Error al obtener sacas: " . $e->getMessage());
        return [];
    }
}
/**
 * ✅ AGREGAR ESTE MÉTODO AL MODELO Saca (documento 8)
 * Ubicación: src/Models/Saca.php
 * Agregar después del método actualizar()
 */

/**
 * Actualiza el campo Qr_Code de una saca
 * 
 * @param int $idSaca ID de la saca
 * @param string $nombreArchivo Nombre del archivo QR
 * @return bool True si se actualizó correctamente
 */
public function actualizarQR(int $idSaca, string $nombreArchivo): bool
{
    try {
        $stmt = $this->db->prepare("
            UPDATE sacas 
            SET Qr_Code = :qr_code 
            WHERE ID_Saca = :id
        ");
        
        $resultado = $stmt->execute([
            ':qr_code' => $nombreArchivo,
            ':id' => $idSaca
        ]);
        
        if ($resultado) {
            error_log("✅ QR actualizado en BD para saca ID={$idSaca}: {$nombreArchivo}");
        }
        
        return $resultado;
        
    } catch (PDOException $e) {
        error_log("❌ Error al actualizar QR en BD: " . $e->getMessage());
        return false;
    }
}


public function obtenerPaquetesDeSaca(int $idSaca): array
{
    try {
        $stmt = $this->db->prepare("
            SELECT 
                p.ID_Paquete,
                p.Tracking,
                p.Paquete_Peso,
                p.Paquete_Dimensiones,
                p.Estado,
                COALESCE(p.Prealerta_Descripcion, pr.Prealerta_Descripcion, 'Sin descripción') AS Prealerta_Descripcion,
                p.Fecha_Registro,
                c.Nombres_Cliente,
                c.Apellidos_Cliente,
                c.Telefono_Cliente,
                c.Direccion_Cliente,
                ct.Categoria_Nombre,
                co.Courier_Nombre,
                s.Sucursal_Nombre,
                ds.Fecha_Agregado
            FROM detalle_sacas ds
            INNER JOIN paquetes p ON ds.ID_Paquete = p.ID_Paquete
            INNER JOIN clientes c ON p.ID_Cliente = c.ID_Cliente
            LEFT JOIN prealertas pr ON p.ID_Prealerta = pr.ID_Prealerta
            LEFT JOIN categorias ct ON p.ID_Categoria = ct.ID_Categoria
            LEFT JOIN courier co ON p.ID_Courier = co.ID_Courier
            LEFT JOIN sucursales s ON p.ID_Sucursal = s.ID_Sucursal
            WHERE ds.ID_Saca = :idSaca
            ORDER BY ds.Fecha_Agregado DESC
        ");
        $stmt->execute([':idSaca' => $idSaca]);
        $paquetes = $stmt->fetchAll();
        if (!$paquetes) {
            error_log("⚠️ No se encontraron paquetes para la saca ID=$idSaca");
        }
        return $paquetes;
    } catch (PDOException $e) {
        error_log("Error al obtener paquetes de saca: " . $e->getMessage());
        return [];
    }
}

    public function registrar(array $data) {
        try {
            // Verificar duplicado de código de saca
            $stmtCheck = $this->db->prepare("SELECT * FROM sacas WHERE Codigo_Saca = ?");
            $stmtCheck->execute([$data['Codigo_Saca']]);
            if ($stmtCheck->fetch()) return 'codigo_duplicado';

            $stmt = $this->db->prepare("
                INSERT INTO sacas (Codigo_Saca, ID_Usuario, ID_Sucursal, Estado, Peso_Total)
                VALUES (?, ?, ?, ?, ?)
            ");
            $resultado = $stmt->execute([
                $data['Codigo_Saca'],
                $data['ID_Usuario'],
                $data['ID_Sucursal'],
                $data['Estado'] ?? 'Pendiente',
                $data['Peso_Total'] ?? 0
            ]);

            return $resultado ? 'registro_exitoso' : 'error_registro';
        } catch (PDOException $e) {
            error_log("Error al registrar saca: " . $e->getMessage());
            return 'error_bd';
        }
    }

    public function obtenerPorId($id) {
        try {
            $stmt = $this->db->prepare("SELECT * FROM sacas WHERE ID_Saca = ?");
            $stmt->execute([$id]);
            return $stmt->fetch(\PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error al obtener saca por ID: " . $e->getMessage());
            return false;
        }
    }

    public function actualizar(array $data) {
        try {
            // Verificar duplicado de código de saca excluyendo el actual
            $stmtCheck = $this->db->prepare("SELECT * FROM sacas WHERE Codigo_Saca = ? AND ID_Saca != ?");
            $stmtCheck->execute([$data['Codigo_Saca'], $data['ID_Saca']]);
            if ($stmtCheck->fetch()) return 'codigo_duplicado';

            $stmt = $this->db->prepare("
                UPDATE sacas
                SET Codigo_Saca = ?, ID_Usuario = ?, ID_Sucursal = ?, Estado = ?, Peso_Total = ?
                WHERE ID_Saca = ?
            ");
            return $stmt->execute([
                $data['Codigo_Saca'],
                $data['ID_Usuario'],
                $data['ID_Sucursal'],
                $data['Estado'],
                $data['Peso_Total'],
                $data['ID_Saca']
            ]);
        } catch (PDOException $e) {
            error_log("Error al actualizar saca: " . $e->getMessage());
            return false;
        }
    }

public function eliminar($id)
{
    try {
        // 🔹 Verificar si la saca tiene paquetes asociados
        $stmtCheck = $this->db->prepare("SELECT COUNT(*) FROM detalle_sacas WHERE ID_Saca = ?");
        $stmtCheck->execute([$id]);
        $tienePaquetes = $stmtCheck->fetchColumn();

        if ($tienePaquetes > 0) {
            return 'saca_con_paquetes'; // ⚠️ No eliminar, tiene paquetes relacionados
        }

        // 🔹 Intentar eliminar si no tiene paquetes
        $stmt = $this->db->prepare("DELETE FROM sacas WHERE ID_Saca = ?");
        $stmt->execute([$id]);

        return $stmt->rowCount() > 0 ? true : false;

    } catch (PDOException $e) {
        error_log("Error al eliminar saca: " . $e->getMessage());
        return false;
    }
}

    public function generarCodigo() {
    try {
        do {
            $codigo = 'SACA-' . strtoupper(substr(md5(uniqid(rand(), true)), 0, 8));
            $stmt = $this->db->prepare("SELECT ID_Saca FROM sacas WHERE Codigo_Saca = ?");
            $stmt->execute([$codigo]);
        } while ($stmt->fetch()); // Repetir si ya existe
        return $codigo;
    } catch (PDOException $e) {
        error_log("Error al generar código de saca: " . $e->getMessage());
        return false;
    }
}

public function obtenerPaquetes($idSaca) {
    $detalleModel = new DetalleSaca();
    return $detalleModel->obtenerPorSaca($idSaca);
}


}
