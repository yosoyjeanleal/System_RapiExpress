<?php
namespace RapiExpress\Interface;

/**
 * Interface IConexion
 * Define los métodos que deben implementar las clases de conexión a base de datos.
 */
interface IConexion
{
    /**
     * Inicializa la conexión a la base de datos.
     * Debe establecer la conexión PDO y configurar los atributos necesarios.
     *
     * @return void
     */
    public function inicializarConexion(): void;
}
