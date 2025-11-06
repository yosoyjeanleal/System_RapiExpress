<?php
namespace RapiExpress\Interface;


interface IDetalleSacaModel
{

    public function obtenerPorSaca(int $idSaca): array;

    public function agregarPaquete(int $idSaca, int $idPaquete): string ;

    public function quitarPaquete(int $idPaquete, int $idSaca): bool;

    public function actualizarPesoSaca(int $idSaca): bool;
}
