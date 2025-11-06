<?php
namespace RapiExpress\Interface;


interface IAuthModel
{
  
    public function validarUsername(string $username): bool;

  
    public function validarPassword(string $password): bool;

  
    public function autenticar(string $username, string $password): ?array;

 
    public function actualizarPassword(string $username, string $newPassword): bool;

  
    public function usuarioExiste(string $username): bool;
}
