<?php
namespace RapiExpress\Models;

use RapiExpress\Config\Conexion;
use PDO;
use PDOException;
use RapiExpress\Interface\IAuthModel;

/**
 * Modelo de autenticación
 * Responsabilidad única: Gestionar operaciones de autenticación y usuarios
 */
class Auth extends Conexion  implements IAuthModel {

    private const USERNAME_PATTERN = '/^[a-zA-Z0-9_]{3,20}$/';
    private const PASSWORD_PATTERN = '/^(?=.*[A-Z])(?=.*[a-z])(?=.*\d)(?=.*[@$!%*#?&]).{8,}$/';

    /**
     * Valida el formato del nombre de usuario
     * @param string $username
     * @return bool
     */
    public function validarUsername(string $username): bool {
        return preg_match(self::USERNAME_PATTERN, $username) === 1;
    }

    /**
     * Valida el formato de la contraseña
     * Requisitos: mínimo 8 caracteres, 1 mayúscula, 1 minúscula, 1 número, 1 carácter especial
     * @param string $password
     * @return bool
     */
    public function validarPassword(string $password): bool {
        return preg_match(self::PASSWORD_PATTERN, $password) === 1;
    }

    /**
     * Autentica un usuario con username y contraseña
     * @param string $username
     * @param string $password
     * @return array|null Datos del usuario si es válido, null si falla
     * @throws PDOException
     */
    public function autenticar(string $username, string $password): ?array {
        try {
            $sql = "SELECT u.*, i.imagen_archivo 
                    FROM usuarios u
                    LEFT JOIN imagenes i ON u.ID_Imagen = i.ID_Imagen
                    WHERE u.Username = ? LIMIT 1";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$username]);
            $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($usuario && password_verify($password, $usuario['Password'])) {
                unset($usuario['Password']); // No retornar el hash
                return $usuario;
            }
            
            return null;
        } catch (PDOException $e) {
            error_log("Error en autenticar: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Actualiza la contraseña de un usuario
     * @param string $username
     * @param string $newPassword
     * @return bool
     * @throws PDOException
     */
    public function actualizarPassword(string $username, string $newPassword): bool {
        try {
            if (!$this->usuarioExiste($username)) {
                return false;
            }

            $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
            
            $stmt = $this->db->prepare("UPDATE usuarios SET Password = ? WHERE Username = ?");
            return $stmt->execute([$hashedPassword, $username]);
            
        } catch (PDOException $e) {
            error_log("Error al actualizar password: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Verifica si un usuario existe
     * @param string $username
     * @return bool
     * @throws PDOException
     */
    public function usuarioExiste(string $username): bool {
        try {
            $stmt = $this->db->prepare("SELECT 1 FROM usuarios WHERE Username = ? LIMIT 1");
            $stmt->execute([$username]);
            
            return $stmt->fetch() !== false;
        } catch (PDOException $e) {
            error_log("Error en usuarioExiste: " . $e->getMessage());
            throw $e;
        }
    }
}