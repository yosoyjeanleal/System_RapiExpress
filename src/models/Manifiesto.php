<?php
namespace RapiExpress\Models;

use RapiExpress\Config\Conexion;
use PDOException;
use RapiExpress\Interface\IManifiestoModel;

class Manifiesto extends Conexion implements IManifiestoModel {

    public function obtenerTodos(): array {
        try {
            $stmt = $this->db->prepare("
                SELECT m.*, s.Codigo_Saca, u.Nombres_Usuario, u.Apellidos_Usuario
                FROM manifiestos m
                INNER JOIN sacas s ON m.ID_Saca = s.ID_Saca
                INNER JOIN usuarios u ON m.ID_Usuario = u.ID_Usuario
                ORDER BY m.ID_Manifiesto DESC
            ");
            $stmt->execute();
            return $stmt->fetchAll();
        } catch(PDOException $e) {
            error_log("Error al obtener manifiestos: " . $e->getMessage());
            return [];
        }
    }

    public function registrar(int $idSaca, int $idUsuario, string $rutaPDF): bool {
        try {
            $stmt = $this->db->prepare("
                INSERT INTO manifiestos (ID_Saca, ID_Usuario, Ruta_PDF)
                VALUES (:idSaca, :idUsuario, :rutaPDF)
            ");
            return $stmt->execute([
                ':idSaca'    => $idSaca,
                ':idUsuario' => $idUsuario,
                ':rutaPDF'   => $rutaPDF
            ]);
        } catch(PDOException $e) {
            error_log("Error al registrar manifiesto: " . $e->getMessage());
            return false;
        }
    }

    public function eliminar(int $id): bool {
        try {
            $stmt = $this->db->prepare("DELETE FROM manifiestos WHERE ID_Manifiesto = :id");
            return $stmt->execute([':id' => $id]);
        } catch(PDOException $e) {
            error_log("Error al eliminar manifiesto: " . $e->getMessage());
            return false;
        }
    }

    public function obtenerPaquetesDeSaca(int $idSaca): array {
        // Reutilizamos modelo DetalleSaca
        $detalle = new DetalleSaca();
        return $detalle->obtenerPorSaca($idSaca);
    }

    
}
