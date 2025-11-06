<?php
namespace RapiExpress\Models;

use RapiExpress\Config\Conexion;
use PDO;
use PDOException;
use RapiExpress\Interface\IPrealertaModel;


class Prealerta extends Conexion implements IPrealertaModel
{
    /** ===========================================
         *  VALIDACIONES INTERNAS
         *  =========================================== */

        private function validarTracking(string $tracking): bool {
            // Solo letras, números y guiones permitidos (máx. 50)
            return preg_match('/^[A-Za-z0-9\-]{3,50}$/', $tracking);
        }

        private function validarDescripcion(?string $descripcion): bool {
            // Permite texto libre pero limitado en longitud
            return strlen(trim($descripcion)) <= 255;
        }

        private function validarPeso($peso): bool {
            return is_numeric($peso) && $peso > 0;
        }

        private function validarPiezas($piezas): bool {
            return is_numeric($piezas) && intval($piezas) > 0;
        }

        private function existeTracking(string $tracking): bool {
            $stmt = $this->db->prepare("SELECT COUNT(*) FROM prealertas WHERE Tracking_Tienda = :tracking");
            $stmt->execute([':tracking' => $tracking]);
            return $stmt->fetchColumn() > 0;
        }

        /** ===========================================
         *  OBTENER DATOS
         *  =========================================== */

        public function obtenerTodos() {
            $stmt = $this->db->prepare("
                SELECT p.*, c.Nombres_Cliente, c.Apellidos_Cliente,
                    t.Tienda_Nombre, cs.Casillero_Nombre, s.Sucursal_Nombre,
                    u.Nombres_Usuario, u.Apellidos_Usuario
                FROM prealertas p
                LEFT JOIN clientes c ON p.ID_Cliente = c.ID_Cliente
                LEFT JOIN tiendas t ON p.ID_Tienda = t.ID_Tienda
                LEFT JOIN casilleros cs ON p.ID_Casillero = cs.ID_Casillero
                LEFT JOIN sucursales s ON p.ID_Sucursal = s.ID_Sucursal
                LEFT JOIN usuarios u ON p.ID_Usuario = u.ID_Usuario
                ORDER BY p.ID_Prealerta DESC
            ");
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }

        public function obtenerPorId($id) {
            $stmt = $this->db->prepare("SELECT * FROM prealertas WHERE ID_Prealerta = :id");
            $stmt->execute([':id' => $id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        }

        public function obtenerPorTrackingTienda($tracking) {
            $stmt = $this->db->prepare("
                SELECT p.*, c.Nombres_Cliente, c.Apellidos_Cliente, t.Tienda_Nombre
                FROM prealertas p
                LEFT JOIN clientes c ON p.ID_Cliente = c.ID_Cliente
                LEFT JOIN tiendas t ON p.ID_Tienda = t.ID_Tienda
                WHERE p.Tracking_Tienda = :tracking
                LIMIT 1
            ");
            $stmt->execute([':tracking' => $tracking]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        }

        /** ===========================================
         *  REGISTRAR
         *  =========================================== */
        public function registrar($data) {
            try {
                // Validaciones
                if (empty($data['ID_Cliente']) || empty($data['Tracking_Tienda']))
                    return 'faltan_datos';

                if (!$this->validarTracking($data['Tracking_Tienda']))
                    return 'tracking_invalido';

                if ($this->existeTracking($data['Tracking_Tienda']))
                    return 'duplicado_tracking';

                if (!$this->validarPiezas($data['Prealerta_Piezas']) || !$this->validarPeso($data['Prealerta_Peso']))
                    return 'dato_invalido';

                if (!$this->validarDescripcion($data['Prealerta_Descripcion']))
                    return 'descripcion_invalida';

                $stmt = $this->db->prepare("
                    INSERT INTO prealertas 
                    (ID_Cliente, ID_Tienda, ID_Usuario, ID_Casillero, ID_Sucursal, 
                    Tracking_Tienda, Prealerta_Piezas, Prealerta_Peso, Prealerta_Descripcion, Estado) 
                    VALUES (:cliente, :tienda, :usuario, :casillero, :sucursal, 
                            :tracking, :piezas, :peso, :descripcion, 'Prealerta')
                ");

                $stmt->execute([
                    ':cliente' => $data['ID_Cliente'],
                    ':tienda' => $data['ID_Tienda'],
                    ':usuario' => $data['ID_Usuario'],
                    ':casillero' => $data['ID_Casillero'],
                    ':sucursal' => $data['ID_Sucursal'],
                    ':tracking' => $data['Tracking_Tienda'],
                    ':piezas' => $data['Prealerta_Piezas'],
                    ':peso' => $data['Prealerta_Peso'],
                    ':descripcion' => $data['Prealerta_Descripcion']
                ]);

                return 'registro_exitoso';
            } catch (PDOException $e) {
                return 'error_bd';
            }
        }

        /** ===========================================
         *  EDITAR
         *  =========================================== */
        public function editar($id, $data) {
            try {
                $actual = $this->obtenerPorId($id);
                if (!$actual) return 'no_existe';

                if (!$this->validarTracking($data['Tracking_Tienda']))
                    return 'tracking_invalido';

                // Evitar duplicado de tracking con otro registro
                $stmt = $this->db->prepare("SELECT COUNT(*) FROM prealertas WHERE Tracking_Tienda = :tracking AND ID_Prealerta != :id");
                $stmt->execute([':tracking' => $data['Tracking_Tienda'], ':id' => $id]);
                if ($stmt->fetchColumn() > 0)
                    return 'duplicado_tracking';

                if (!$this->validarPiezas($data['Prealerta_Piezas']) || !$this->validarPeso($data['Prealerta_Peso']))
                    return 'dato_invalido';

                if (!$this->validarDescripcion($data['Prealerta_Descripcion']))
                    return 'descripcion_invalida';

                $stmt = $this->db->prepare("
                    UPDATE prealertas SET
                        ID_Cliente=:cliente,
                        ID_Tienda=:tienda,
                        ID_Casillero=:casillero,
                        ID_Sucursal=:sucursal,
                        Tracking_Tienda=:tracking,
                        Prealerta_Piezas=:piezas,
                        Prealerta_Peso=:peso,
                        Prealerta_Descripcion=:descripcion,
                        Estado=:estado
                    WHERE ID_Prealerta=:id
                ");

                $stmt->execute([
                    ':cliente' => $data['ID_Cliente'],
                    ':tienda' => $data['ID_Tienda'],
                    ':casillero' => $data['ID_Casillero'],
                    ':sucursal' => $data['ID_Sucursal'],
                    ':tracking' => $data['Tracking_Tienda'],
                    ':piezas' => $data['Prealerta_Piezas'],
                    ':peso' => $data['Prealerta_Peso'],
                    ':descripcion' => $data['Prealerta_Descripcion'],
                    ':estado' => $data['Estado'],
                    ':id' => $id
                ]);

                return 'actualizacion_exitosa';
            } catch (PDOException $e) {
                return 'error_bd';
            }
        }

        /** ===========================================
     *  ELIMINAR
     *  =========================================== */
    public function eliminar($id) {
        try {
            // Verificar existencia
            $prealerta = $this->obtenerPorId($id);
            if (!$prealerta) return 'no_existe';

            // Verificar si ya está consolidada (tiene paquete asociado)
            $stmt = $this->db->prepare("SELECT COUNT(*) FROM paquetes WHERE ID_Prealerta = :id");
            $stmt->execute([':id' => $id]);
            if ($stmt->fetchColumn() > 0) {
                return 'tiene_paquete'; // No permitir eliminar si ya tiene paquete
            }

            $stmt = $this->db->prepare("DELETE FROM prealertas WHERE ID_Prealerta = :id");
            $stmt->execute([':id' => $id]);
            return 'eliminado';
        } catch (PDOException $e) {
            // Si hay FK en uso
            if ($e->getCode() == '23000') return 'relacion_existente';
            return 'error_bd';
        }
    }

    /** ===========================================
     *  ELIMINAR DESPUÉS DE CONSOLIDACIÓN
     *  =========================================== */
    public function eliminarDespuesDeConsolidar($id) {
        try {
            $stmt = $this->db->prepare("DELETE FROM prealertas WHERE ID_Prealerta = :id");
            $stmt->execute([':id' => $id]);
            return true;
        } catch (PDOException $e) {
            error_log("Error al eliminar prealerta consolidada: " . $e->getMessage());
            return false;
        }
    }
}