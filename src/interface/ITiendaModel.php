<?php
namespace RapiExpress\Interfaces;

interface ITienda
{

    public function registrar(array $data): string;

    public function actualizar(array $data): string|bool;

    public function eliminar(int $id): string|bool;

    public function obtenerTodas(): array;

    public function obtenerPorId(int $id): ?array;
}
