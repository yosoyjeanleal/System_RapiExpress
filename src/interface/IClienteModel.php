<?php
namespace RapiExpress\Interface;

/**
 * Interface IClienteModel
 * Define las operaciones del modelo Cliente.
 */
interface IClienteModel
{

    public function registrar(array $data);

    public function actualizar(array $data);

    public function eliminar($id);

    public function obtenerTodos();

    public function obtenerPorId($id);
}
