<?php
namespace RapiExpress\Models;

use RapiExpress\Config\Conexion;
use PDO;
use PDOException;
use RapiExpress\Interface\IPaqueteModel;

// importar el helper global para generar QR
require_once __DIR__ . '/../helpers/qr.php';

class Paquete extends Conexion implements IPaqueteModel {

    /** ============================================================
     *  VALIDACIONES INTERNAS
     *  ============================================================ */
    private function validarDatos($data, $isEdit = false)
    {
        $errores = [];

        // Campos obligatorios
        if (empty($data['ID_Cliente'])) {
            $errores[] = 'El cliente es obligatorio.';
        }
        if (empty($data['ID_Categoria'])) {
            $errores[] = 'Debe seleccionar una categoría.';
        }
        if (empty($data['ID_Courier'])) {
            $errores[] = 'Debe seleccionar un courier.';
        }
        if (empty($data['ID_Sucursal'])) {
            $errores[] = 'Debe seleccionar una sucursal.';
        }
        if (empty($data['Paquete_Peso']) || !is_numeric($data['Paquete_Peso'])) {
            $errores[] = 'El peso del paquete debe ser un número válido.';
        }

        // Validar existencia de cliente
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM clientes WHERE ID_Cliente = :id");
        $stmt->execute([':id' => $data['ID_Cliente']]);
        if ($stmt->fetchColumn() == 0) {
            $errores[] = 'El cliente seleccionado no existe.';
        }

        // Validar existencia de categoría
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM categorias WHERE ID_Categoria = :id");
        $stmt->execute([':id' => $data['ID_Categoria']]);
        if ($stmt->fetchColumn() == 0) {
            $errores[] = 'La categoría seleccionada no existe.';
        }

        // Validar existencia de courier
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM courier WHERE ID_Courier = :id");
        $stmt->execute([':id' => $data['ID_Courier']]);
        if ($stmt->fetchColumn() == 0) {
            $errores[] = 'El courier seleccionado no existe.';
        }

        // Validar existencia de sucursal
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM sucursales WHERE ID_Sucursal = :id");
        $stmt->execute([':id' => $data['ID_Sucursal']]);
        if ($stmt->fetchColumn() == 0) {
            $errores[] = 'La sucursal seleccionada no existe.';
        }

        // Evitar duplicados de tracking (solo en creación)
        if (!$isEdit && !empty($data['Tracking'])) {
            $stmt = $this->db->prepare("SELECT COUNT(*) FROM paquetes WHERE Tracking = :t");
            $stmt->execute([':t' => $data['Tracking']]);
            if ($stmt->fetchColumn() > 0) {
                $errores[] = 'El número de tracking ya existe.';
            }
        }

        return $errores;
    }

    /** ============================================================
     *  OBTENER TODOS
     *  ============================================================ */
    public function obtenerTodos()
    {
        $stmt = $this->db->prepare("
            SELECT pk.*, 
                   c.Nombres_Cliente, c.Apellidos_Cliente, 
                   ct.Categoria_Nombre, 
                   cr.Courier_Nombre, 
                   u.Nombres_Usuario,
                   s.Sucursal_Nombre
            FROM paquetes pk
            LEFT JOIN clientes c ON pk.ID_Cliente = c.ID_Cliente
            LEFT JOIN categorias ct ON pk.ID_Categoria = ct.ID_Categoria
            LEFT JOIN courier cr ON pk.ID_Courier = cr.ID_Courier
            LEFT JOIN usuarios u ON pk.ID_Usuario = u.ID_Usuario
            LEFT JOIN sucursales s ON pk.ID_Sucursal = s.ID_Sucursal
            ORDER BY pk.ID_Paquete DESC
        ");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /** ============================================================
 *  REGISTRAR PAQUETE (CON PIEZAS)
 *  ============================================================ */
public function registrar($data)
{
    try {
        // 🔍 Validar datos
        $errores = $this->validarDatos($data);
        if (!empty($errores)) {
            return ['success' => false, 'errores' => $errores];
        }

        // Generar tracking si no existe
        if (empty($data['Tracking'])) {
            $data['Tracking'] = $this->generarTracking();
        }

        // Obtener nombres relacionados
        $cliente = $this->obtenerNombreCliente($data['ID_Cliente']);
        $sucursal = $this->obtenerNombreSucursal($data['ID_Sucursal']);
        $courier = $this->obtenerNombreCourier($data['ID_Courier']);

        // Generar QR
        $qrPath = generar_qr_code('paquete', [
            'Tracking'     => $data['Tracking'],
            'Cliente'      => $cliente,
            'Instrumento'  => $data['Nombre_Instrumento'] ?? 'Sin instrumento',
            'Peso'         => $data['Paquete_Peso'],
            'Piezas'       => $data['Paquete_Piezas'] ?? 1, // ✅ AGREGAR PIEZAS AL QR
            'Sucursal'     => $sucursal,
            'Courier'      => $courier,
            'Descripcion'  => $data['Prealerta_Descripcion'] ?? '',
        ], __DIR__ . '/../storage/qr/');

        // Insertar
        $stmt = $this->db->prepare("
            INSERT INTO paquetes
                (ID_Prealerta, ID_Usuario, ID_Cliente, Nombre_Instrumento, ID_Categoria, 
                 ID_Sucursal, Tracking, ID_Courier, Prealerta_Descripcion, Paquete_Peso, 
                 Paquete_Piezas, Estado, Qr_code)
            VALUES
                (:prealerta, :usuario, :cliente, :instrumento, :categoria, 
                 :sucursal, :tracking, :courier, :descripcion, :peso, 
                 :piezas, :estado, :qr)
        ");
        $stmt->execute([
            ':prealerta'    => $data['ID_Prealerta'] ?? null,
            ':usuario'      => $data['ID_Usuario'] ?? null,
            ':cliente'      => $data['ID_Cliente'],
            ':instrumento'  => $data['Nombre_Instrumento'] ?? null,
            ':categoria'    => $data['ID_Categoria'],
            ':sucursal'     => $data['ID_Sucursal'],
            ':tracking'     => $data['Tracking'],
            ':courier'      => $data['ID_Courier'],
            ':descripcion'  => $data['Prealerta_Descripcion'],
            ':peso'         => $data['Paquete_Peso'],
            ':piezas'       => $data['Paquete_Piezas'] ?? 1, // ✅ INSERTAR PIEZAS
            ':estado'       => $data['Estado'] ?? 'En tránsito',
            ':qr'           => basename($qrPath)
        ]);

        return ['success' => true];
    } catch (PDOException $e) {
        return ['success' => false, 'errores' => ['Error BD: ' . $e->getMessage()]];
    }
}

    /** ============================================================
     *  EDITAR PAQUETE
     *  ============================================================ */
    public function editar($id, $data)
    {
        try {
            $errores = $this->validarDatos($data, true);
            if (!empty($errores)) {
                return ['success' => false, 'errores' => $errores];
            }

            $paqueteActual = $this->obtenerPorId($id);
            if (!$paqueteActual) {
                return ['success' => false, 'errores' => ['El paquete no existe.']];
            }

            $cliente = $this->obtenerNombreCliente($data['ID_Cliente']);
            $sucursal = $this->obtenerNombreSucursal($data['ID_Sucursal']);
            $courier = $this->obtenerNombreCourier($data['ID_Courier']);

            $rutaQR = __DIR__ . '/../storage/qr/';
            if (!empty($paqueteActual['Qr_code'])) {
                $qrViejo = $rutaQR . $paqueteActual['Qr_code'];
                if (file_exists($qrViejo)) unlink($qrViejo);
            }

            $qrPath = generar_qr_code('paquete', [
                'Tracking'     => $paqueteActual['Tracking'],
                'Cliente'      => $cliente,
                'Instrumento'  => $data['Nombre_Instrumento'] ?? $paqueteActual['Nombre_Instrumento'],
                'Peso'         => $data['Paquete_Peso'] ?? $paqueteActual['Paquete_Peso'],
                'Sucursal'     => $sucursal,
                'Courier'      => $courier,
                'Descripcion'  => $data['Prealerta_Descripcion'] ?? $paqueteActual['Prealerta_Descripcion'],
            ], $rutaQR);

            $stmt = $this->db->prepare("
                UPDATE paquetes SET
                    ID_Cliente = :cliente,
                    Nombre_Instrumento = :instrumento,
                    ID_Categoria = :categoria,
                    ID_Sucursal = :sucursal,
                    ID_Courier = :courier,
                    Prealerta_Descripcion = :descripcion,
                    Paquete_Peso = :peso,
                    Estado = :estado,
                    Qr_code = :qr
                WHERE ID_Paquete = :id
            ");

            $stmt->execute([
                ':cliente'     => $data['ID_Cliente'],
                ':instrumento' => $data['Nombre_Instrumento'] ?? null,
                ':categoria'   => $data['ID_Categoria'],
                ':sucursal'    => $data['ID_Sucursal'],
                ':courier'     => $data['ID_Courier'],
                ':descripcion' => $data['Prealerta_Descripcion'],
                ':peso'        => $data['Paquete_Peso'],
                ':estado'      => $data['Estado'] ?? 'En tránsito',
                ':qr'          => basename($qrPath),
                ':id'          => $id
            ]);

            return ['success' => true];
        } catch (PDOException $e) {
            return ['success' => false, 'errores' => ['Error BD: ' . $e->getMessage()]];
        }
    }

    /** ============================================================
     *  AUXILIARES
     *  ============================================================ */
    private function obtenerNombreCliente($id)
    {
        $stmt = $this->db->prepare("SELECT CONCAT(Nombres_Cliente, ' ', Apellidos_Cliente) AS Nombre FROM clientes WHERE ID_Cliente = :id");
        $stmt->execute([':id' => $id]);
        $r = $stmt->fetch(PDO::FETCH_ASSOC);
        return $r['Nombre'] ?? 'N/A';
    }

    private function obtenerNombreSucursal($id)
    {
        $stmt = $this->db->prepare("SELECT Sucursal_Nombre FROM sucursales WHERE ID_Sucursal = :id");
        $stmt->execute([':id' => $id]);
        $r = $stmt->fetch(PDO::FETCH_ASSOC);
        return $r['Sucursal_Nombre'] ?? 'N/A';
    }

    private function obtenerNombreCourier($id)
    {
        $stmt = $this->db->prepare("SELECT Courier_Nombre FROM courier WHERE ID_Courier = :id");
        $stmt->execute([':id' => $id]);
        $r = $stmt->fetch(PDO::FETCH_ASSOC);
        return $r['Courier_Nombre'] ?? 'N/A';
    }

public function eliminar($id)
{
    try {
        // Verificar si el paquete existe en detalle_sacas
        $stmt = $this->db->prepare("
            SELECT COUNT(*) as total 
            FROM detalle_sacas 
            WHERE ID_Paquete = :id
        ");
        $stmt->execute([':id' => $id]);
        $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($resultado['total'] > 0) {
            return [
                'success' => false, 
                'errores' => ['No se puede eliminar este paquete porque está asignado a una o más sacas.']
            ];
        }
        
        // Verificar si el paquete existe en seguimientos
        $stmt = $this->db->prepare("
            SELECT COUNT(*) as total 
            FROM seguimientos 
            WHERE ID_Paquete = :id
        ");
        $stmt->execute([':id' => $id]);
        $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($resultado['total'] > 0) {
            return [
                'success' => false, 
                'errores' => ['No se puede eliminar este paquete porque tiene seguimientos asociados.']
            ];
        }
        
        // Verificar si el paquete existe en manifiestos (si hay relación directa)
        $stmt = $this->db->prepare("
            SELECT COUNT(*) as total 
            FROM manifiestos m
            INNER JOIN detalle_sacas ds ON m.ID_Saca = ds.ID_Saca
            WHERE ds.ID_Paquete = :id
        ");
        $stmt->execute([':id' => $id]);
        $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($resultado['total'] > 0) {
            return [
                'success' => false, 
                'errores' => ['No se puede eliminar este paquete porque está asociado a manifiestos.']
            ];
        }
        
        // Obtener información del paquete antes de eliminar (para borrar el QR)
        $paquete = $this->obtenerPorId($id);
        
        // Si todo está bien, proceder a eliminar
        $stmt = $this->db->prepare("DELETE FROM paquetes WHERE ID_Paquete = :id");
        $resultado = $stmt->execute([':id' => $id]);
        
        // Si se eliminó correctamente, eliminar el archivo QR
        if ($resultado && !empty($paquete['Qr_code'])) {
            $rutaQR = __DIR__ . '/../storage/qr/' . $paquete['Qr_code'];
            if (file_exists($rutaQR)) {
                unlink($rutaQR);
            }
        }
        
        return $resultado;
        
    } catch (PDOException $e) {
        error_log("Error al eliminar paquete: " . $e->getMessage());
        
        // Manejo específico de errores de clave foránea
        if ($e->getCode() == '23000') {
            return [
                'success' => false, 
                'errores' => ['No se puede eliminar este paquete porque está relacionado con otros registros en el sistema.']
            ];
        }
        
        return [
            'success' => false, 
            'errores' => ['Error al eliminar el paquete: ' . $e->getMessage()]
        ];
    }
}


    public function obtenerPorId($id)
    {
        $stmt = $this->db->prepare("SELECT * FROM paquetes WHERE ID_Paquete = :id");
        $stmt->execute([':id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function generarTracking()
    {
        return 'PKG-' . str_pad(rand(1, 99999), 5, '0', STR_PAD_LEFT);
    }
    
public function obtenerSinSaca(): array {
    try {
        $stmt = $this->db->prepare("
            SELECT p.*, c.Nombres_Cliente, c.Apellidos_Cliente
            FROM paquetes p
            LEFT JOIN clientes c ON p.ID_Cliente = c.ID_Cliente
            WHERE p.ID_Paquete NOT IN (
                SELECT ID_Paquete FROM detalle_sacas
            )
            AND p.Estado = 'En tránsito'
        ");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log('Error al obtener paquetes sin saca: ' . $e->getMessage());
        return [];
    }
}



public function obtenerPorSacaPendiente() {
    $stmt = $this->db->query("SELECT * FROM paquetes WHERE ID_Saca IS NULL AND Estado = 'En tránsito'");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}



   
    /**
     * ✅ NUEVO MÉTODO: Obtener paquete por Tracking
     */
    public function obtenerPorTracking($tracking)
    {
        $stmt = $this->db->prepare("
            SELECT pk.*, 
                   c.Nombres_Cliente, c.Apellidos_Cliente, 
                   ct.Categoria_Nombre, 
                   cr.Courier_Nombre, 
                   s.Sucursal_Nombre
            FROM paquetes pk
            LEFT JOIN clientes c ON pk.ID_Cliente = c.ID_Cliente
            LEFT JOIN categorias ct ON pk.ID_Categoria = ct.ID_Categoria
            LEFT JOIN courier cr ON pk.ID_Courier = cr.ID_Courier
            LEFT JOIN sucursales s ON pk.ID_Sucursal = s.ID_Sucursal
            WHERE pk.Tracking = :tracking
            LIMIT 1
        ");
        $stmt->execute([':tracking' => $tracking]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Resto de tus métodos...
}