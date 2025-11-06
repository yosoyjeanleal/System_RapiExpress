<?php
namespace RapiExpress\Models;

use PDO;
use PDOException;
use RapiExpress\Config\Conexion;
use RapiExpress\Interface\ICategoriaModel;

class Categoria extends Conexion implements ICategoriaModel
{
    // ============================================================
    // VALIDACIONES
    // ============================================================

    private function validarNombre(string $nombre): bool
    {
        $regex = '/^[A-Za-z0-9\sáéíóúÁÉÍÓÚñÑ.,\-()]{3,50}$/u';
        return preg_match($regex, trim($nombre)) === 1;
    }

    private function validarNumero($valor, float $min = 0, bool $entero = false): bool
    {
        if (!is_numeric($valor)) return false;
        if ($entero && floor($valor) != $valor) return false;
        return floatval($valor) >= $min;
    }

    private function validarDatos(array $data, bool $edicion = false): array
    {
        $errores = [];

        if ($edicion && (empty($data['ID_Categoria']) || !is_numeric($data['ID_Categoria']))) {
            $errores[] = "ID de categoría inválido.";
        }

        if (empty($data['nombre']) || !$this->validarNombre($data['nombre'])) {
            $errores[] = "El nombre debe tener entre 3 y 50 caracteres y solo contener letras, números y (,.-()).";
        }

        if (!$this->validarNumero($data['altura'], 0)) $errores[] = "Altura inválida.";
        if (!$this->validarNumero($data['largo'], 0)) $errores[] = "Largo inválido.";
        if (!$this->validarNumero($data['ancho'], 0)) $errores[] = "Ancho inválido.";
        if (!$this->validarNumero($data['peso'], 0)) $errores[] = "Peso inválido.";
        if (!$this->validarNumero($data['precio'], 0)) $errores[] = "Precio inválido.";
        if (!$this->validarNumero($data['piezas'], 1, true)) $errores[] = "Piezas debe ser un número entero ≥ 1.";

        return $errores;
    }

    // ============================================================
    // CRUD
    // ============================================================

    public function registrar(array $data): array
    {
        $errores = $this->validarDatos($data);
        if (!empty($errores)) return ['success' => false, 'mensaje' => implode(' ', $errores)];

        if ($this->verificarCategoriaExistente($data['nombre'])) {
            return ['success' => false, 'mensaje' => 'Ya existe una categoría con ese nombre.'];
        }

        try {
            $sql = "INSERT INTO categorias (
                        Categoria_Nombre, Categoria_Altura, Categoria_Largo,
                        Categoria_Ancho, Categoria_Peso, Categoria_Piezas, Categoria_Precio
                    ) VALUES (?, ?, ?, ?, ?, ?, ?)";

            $stmt = $this->db->prepare($sql);
            $ok = $stmt->execute([
                trim($data['nombre']),
                floatval($data['altura']),
                floatval($data['largo']),
                floatval($data['ancho']),
                floatval($data['peso']),
                intval($data['piezas']),
                floatval($data['precio'])
            ]);

            return $ok
                ? ['success' => true, 'mensaje' => 'Categoría registrada exitosamente.']
                : ['success' => false, 'mensaje' => 'Error al registrar la categoría.'];
        } catch (PDOException $e) {
            error_log("Error al registrar categoría: " . $e->getMessage());
            return ['success' => false, 'mensaje' => 'Error de base de datos.'];
        }
    }

    public function actualizar(array $data): array
    {
        $errores = $this->validarDatos($data, true);
        if (!empty($errores)) return ['success' => false, 'mensaje' => implode(' ', $errores)];

        if ($this->verificarCategoriaExistente($data['nombre'], $data['ID_Categoria'])) {
            return ['success' => false, 'mensaje' => 'Ya existe otra categoría con ese nombre.'];
        }

        try {
            $sql = "UPDATE categorias SET 
                        Categoria_Nombre = ?, Categoria_Altura = ?, Categoria_Largo = ?, 
                        Categoria_Ancho = ?, Categoria_Peso = ?, Categoria_Piezas = ?, Categoria_Precio = ?
                    WHERE ID_Categoria = ?";
            $stmt = $this->db->prepare($sql);
            $ok = $stmt->execute([
                trim($data['nombre']),
                floatval($data['altura']),
                floatval($data['largo']),
                floatval($data['ancho']),
                floatval($data['peso']),
                intval($data['piezas']),
                floatval($data['precio']),
                intval($data['ID_Categoria'])
            ]);

            return $ok
                ? ['success' => true, 'mensaje' => 'Categoría actualizada exitosamente.']
                : ['success' => false, 'mensaje' => 'Error al actualizar la categoría.'];
        } catch (PDOException $e) {
            error_log("Error al actualizar categoría: " . $e->getMessage());
            return ['success' => false, 'mensaje' => 'Error de base de datos.'];
        }
    }

    // ============================================================
    // ✅ ELIMINAR CATEGORÍA (CON PROTECCIÓN)
    // ============================================================
    public function eliminar($id): array
    {
        try {
            // 🔍 Verificar si la categoría está en uso en la tabla paquetes
            $stmt = $this->db->prepare("SELECT COUNT(*) FROM paquetes WHERE ID_Categoria = ?");
            $stmt->execute([$id]);
            $count = $stmt->fetchColumn();
            
            if ($count > 0) {
                return [
                    'success' => false, 
                    'mensaje' => "No se puede eliminar la categoría porque está asociada a {$count} paquete(s). Primero debe desvincularla o reasignar esos paquetes a otra categoría."
                ];
            }

            // Si no está en uso, proceder con la eliminación
            $stmt = $this->db->prepare("DELETE FROM categorias WHERE ID_Categoria = ?");
            $ok = $stmt->execute([$id]);
            
            return $ok
                ? ['success' => true, 'mensaje' => 'Categoría eliminada exitosamente.']
                : ['success' => false, 'mensaje' => 'Error al eliminar la categoría.'];
                
        } catch (PDOException $e) {
            error_log("Error al eliminar categoría: " . $e->getMessage());
            return ['success' => false, 'mensaje' => 'Error de base de datos: ' . $e->getMessage()];
        }
    }

    public function obtenerTodos(): array
    {
        try {
            $stmt = $this->db->query("SELECT * FROM categorias ORDER BY ID_Categoria DESC");
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error al obtener categorías: " . $e->getMessage());
            return [];
        }
    }

    // ============================================================
    // ✅ OBTENER POR ID (CORREGIDO)
    // ============================================================
    public function obtenerPorId($id): ?array
    {
        try {
            $stmt = $this->db->prepare("SELECT * FROM categorias WHERE ID_Categoria = ?");
            $stmt->execute([$id]);
            $res = $stmt->fetch(PDO::FETCH_ASSOC);
            return $res ?: null;
        } catch (PDOException $e) {
            error_log("Error al obtener categoría por ID: " . $e->getMessage());
            return null; // ✅ CORREGIDO
        }
    }

    public function verificarCategoriaExistente($nombre, $idCategoria = null): bool
    {
        try {
            if ($idCategoria) {
                $stmt = $this->db->prepare("SELECT COUNT(*) FROM categorias WHERE Categoria_Nombre = ? AND ID_Categoria != ?");
                $stmt->execute([$nombre, $idCategoria]);
            } else {
                $stmt = $this->db->prepare("SELECT COUNT(*) FROM categorias WHERE Categoria_Nombre = ?");
                $stmt->execute([$nombre]);
            }
            return $stmt->fetchColumn() > 0;
        } catch (PDOException $e) {
            error_log("Error al verificar categoría existente: " . $e->getMessage());
            return false;
        }
    }
}