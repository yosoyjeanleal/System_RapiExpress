<?php
namespace RapiExpress\Interface;

interface IPrealertaModel
{
    
    public function obtenerTodos();
    public function obtenerPorId(int $id);
    public function obtenerPorTrackingTienda(string $tracking);    
    public function registrar(array $data);
    public function editar(int $id, array $data);
    public function eliminar(int $id);
    public function eliminarDespuesDeConsolidar(int $id);
}
