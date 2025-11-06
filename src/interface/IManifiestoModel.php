<?php
namespace RapiExpress\Interface;

interface IManifiestoModel
{
    /**
     * Obtiene todos los manifiestos registrados con sus relaciones (saca y usuario).
     * @return array
     */
    public function obtenerTodos(): array;

    /**
     * Registra un nuevo manifiesto en la base de datos.
     * @param int $idSaca
     * @param int $idUsuario
     * @param string $rutaPDF
     * @return bool
     */
    public function registrar(int $idSaca, int $idUsuario, string $rutaPDF): bool;

    /**
     * Elimina un manifiesto por su ID.
     * @param int $id
     * @return bool
     */
    public function eliminar(int $id): bool;

    /**
     * Obtiene todos los paquetes asociados a una saca específica.
     * @param int $idSaca
     * @return array
     */
    public function obtenerPaquetesDeSaca(int $idSaca): array;
}
