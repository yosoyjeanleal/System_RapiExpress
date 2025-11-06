<?php
namespace RapiExpress\Interface;

interface IPaqueteModel
{
    /**
     * Obtiene todos los paquetes con sus relaciones (cliente, categoría, courier, sucursal, usuario).
     * @return array
     */
    public function obtenerTodos();

    /**
     * Registra un nuevo paquete con validaciones y generación de QR.
     * @param array $data
     * @return array Resultado con éxito o errores.
     */
    public function registrar(array $data);

    /**
     * Edita un paquete existente.
     * @param int $id
     * @param array $data
     * @return array Resultado con éxito o errores.
     */
    public function editar(int $id, array $data);

    /**
     * Elimina un paquete si no está relacionado con otras entidades.
     * @param int $id
     * @return array|bool Resultado o estado de éxito.
     */
    public function eliminar(int $id);

    /**
     * Obtiene un paquete por su ID.
     * @param int $id
     * @return array|null
     */
    public function obtenerPorId(int $id);

    /**
     * Genera un código de tracking aleatorio.
     * @return string
     */
    public function generarTracking();

    /**
     * Obtiene todos los paquetes que no están asignados a ninguna saca.
     * @return array
     */
    public function obtenerSinSaca(): array;

    /**
     * Obtiene los paquetes que están pendientes por asignar a una saca.
     * @return array
     */
    public function obtenerPorSacaPendiente();

    /**
     * Obtiene la información de un paquete por su número de tracking.
     * @param string $tracking
     * @return array|null
     */
    public function obtenerPorTracking(string $tracking);
}
