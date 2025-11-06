<?php
namespace RapiExpress\Interface;

/**
 * Interface ICategoriaModel
 * Define las operaciones disponibles para el modelo de Categorías.
 */
interface ICategoriaModel
{
    /**
     * Registra una nueva categoría.
     * @param array $data Datos de la categoría.
     * @return array Resultado con claves ['success' => bool, 'mensaje' => string].
     */
    public function registrar(array $data): array;

    /**
     * Actualiza una categoría existente.
     * @param array $data Datos actualizados de la categoría.
     * @return array Resultado con claves ['success' => bool, 'mensaje' => string].
     */
    public function actualizar(array $data): array;

    /**
     * Elimina una categoría por su ID, verificando que no esté en uso.
     * @param int $id ID de la categoría.
     * @return array Resultado con claves ['success' => bool, 'mensaje' => string].
     */
    public function eliminar($id): array;

    /**
     * Obtiene todas las categorías registradas.
     * @return array Lista de categorías.
     */
    public function obtenerTodos(): array;

    /**
     * Obtiene una categoría específica por su ID.
     * @param int $id ID de la categoría.
     * @return array|null Datos de la categoría o null si no existe.
     */
    public function obtenerPorId($id): ?array;

    /**
     * Verifica si ya existe una categoría con el mismo nombre.
     * @param string $nombre Nombre de la categoría.
     * @param int|null $idCategoria ID opcional (para excluir durante edición).
     * @return bool True si existe, false si no.
     */
    public function verificarCategoriaExistente($nombre, $idCategoria = null): bool;
}
