<?php
namespace RapiExpress\Interface;


interface ICourierModel
{

    public function registrar(array $data);

    public function actualizar(array $data);

    public function eliminar($id);

    public function obtenerTodos();

    public function obtenerPorId($id);
}
