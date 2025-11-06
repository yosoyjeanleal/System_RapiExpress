<?php
namespace RapiExpress\Interface;


interface ISeguimientoModel
{
    /**
     * Obtiene todos los seguimientos con información relacionada
     * (cliente y paquete). Puede aplicar un filtro de búsqueda.
     * 
     * @param string|null $filtro Texto a buscar por tracking o nombre de cliente.
     * @return array Lista de seguimientos encontrados.
     */
    public function obtenerTodos(?string $filtro = null);

    /**
     * Registra un nuevo seguimiento.
     * 
     * @param array $data Datos del seguimiento (ID_Cliente, ID_Paquete, Estado).
     * @return bool True si se registró correctamente, false si falló.
     */
    public function registrar(array $data);
}
