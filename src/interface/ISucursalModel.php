<?php
namespace RapiExpress\Interface;

/**
 * Interface ISucursalModel
 * Define los métodos que debe implementar el modelo Sucursal.
 */
interface ISucursalModel
{
    /**
     * Registra una nueva sucursal en la base de datos.
     * 
     * @param array $data Datos de la sucursal (RIF, nombre, dirección, teléfono, correo).
     * @return string Resultado del proceso ('registro_exitoso', 'rif_existente', 'error_bd', etc.).
     */
    public function registrar(array $data): string;

    /**
     * Obtiene todas las sucursales registradas.
     * 
     * @return array Lista de sucursales en formato asociativo.
     */
    public function obtenerTodas(): array;

    /**
     * Obtiene una sucursal específica por su ID.
     * 
     * @param int $id ID de la sucursal.
     * @return array|null Datos de la sucursal o null si no existe.
     */
    public function obtenerPorId(int $id): ?array;

    /**
     * Actualiza la información de una sucursal existente.
     * 
     * @param array $data Datos actualizados de la sucursal (incluye ID_Sucursal).
     * @return string Resultado de la actualización ('actualizacion_exitosa', 'rif_existente', 'error_bd', etc.).
     */
    public function actualizar(array $data): string;

    /**
     * Elimina una sucursal si no está en uso por otras tablas relacionadas.
     * 
     * @param int $id ID de la sucursal.
     * @return string Resultado ('eliminado', 'no_existe', 'error_bd', o JSON con tablas relacionadas).
     */
    public function eliminar(int $id): string;

    /**
     * Devuelve el último error registrado en el modelo.
     * 
     * @return string Mensaje de error o cadena vacía si no hubo error.
     */
    public function getLastError(): string;
}
