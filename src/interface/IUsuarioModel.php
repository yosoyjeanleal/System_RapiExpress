<?php
namespace RapiExpress\Interfaces;

interface IUsuario
{
  
    public function login(string $username, string $password): array;

    public function registrar(array $data): array;

    public function actualizar(array $data): array;

    public function actualizarPerfil(int $id, array $data): array;

    public function subirImagenPerfil(array $file): array;

    public function tieneDependencias(int $id): array;

    public function eliminar(int $id): array;

    public function obtenerTodos(): array;

    public function obtenerPorId(int $id): ?array;

    public function obtenerTodasImagenes(): array;
   
    public function obtenerNombreArchivoPorIdImagen(int $idImagen): ?string;

    public function getNombreCompleto(): string;

 
    
}
