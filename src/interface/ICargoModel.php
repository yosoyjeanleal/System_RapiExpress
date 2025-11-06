<?php
namespace RapiExpress\Interface;


interface CargoInterface
{
   
    public function validarNombre(string $nombre): bool;
  
    public function verificarCargoExistente(string $nombreCargo, ?int $idCargo = null): bool;
 
    public function registrar(array $data): string;
 
    public function actualizar(array $data): string;
 
    public function obtenerTodos(): array;

    public function obtenerPorId(int $id): ?array;

    public function eliminar(int $id): string;
}
