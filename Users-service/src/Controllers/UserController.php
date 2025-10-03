<?php
// Activa el modo estricto de tipos
declare(strict_types=1);

require_once __DIR__ . '/../Models/User.php';

/**
 * Controlador para operaciones de usuarios
 * 
 * Responsabilidades:
 * - Validar entradas
 * - Coordinar con el modelo
 * - Preparar respuestas estructuradas
 */
class UserController
{
    private UserModel $model;

    /**
     * @param UserModel|null $model Modelo de usuarios (inyección de dependencias)
     */
    public function __construct(?UserModel $model = null)
    {
        $this->model = $model ?? new UserModel();
    }

    /**
     * Obtiene todos los usuarios
     * 
     * @return array Estructura: ['status' => int, 'data' => array|null, 'message' => string|null]
     */
    public function getAllUsers(): array
    {
        try {
            $users = $this->model->getAllUsers();

            if (empty($users)) {
                return [
                    'status' => 404,
                    'data' => null,
                    'message' => 'No users found'
                ];
            }

            return [
                'status' => 200,
                'data' => $users,
                'message' => null
            ];
        } catch (Exception $e) {
            // Log del error
            error_log('Error in getAllUsers: ' . $e->getMessage());

            return [
                'status' => 500,
                'data' => null,
                'message' => 'Internal server error'
            ];
        }
    }

    /**
     * Obtiene un usuario por su ID
     * 
     * @param int $id ID del usuario (debe ser mayor a 0)
     * @return array Estructura: ['status' => int, 'data' => array|null, 'message' => string|null]
     */
    public function getUserById(int $id): array
    {

        if ($id <= 0) {
            return [
                'status' => 400,
                'data' => null,
                'message' => 'User ID must be a positive integer'
            ];
        }

        try {
            $user = $this->model->getUser($id);

            if (!$user) {
                return [
                    'status' => 404,
                    'data' => null,
                    'message' => 'User not found'
                ];
            }

            return [
                'status' => 200,
                'data' => $user,
                'message' => null
            ];
        } catch (Exception $e) {
            error_log("Error in getUserById({$id}): " . $e->getMessage());

            return [
                'status' => 500,
                'data' => null,
                'message' => 'Internal server error'
            ];
        }
    }

    /**
     * Crea un nuevo usuario y lo almacena en la db
     * 
     * @param string $name nombre del usuario.
     * @param string $role solo puede ser reader o admin(Por defecto es reader)
     * @return array [id => int|null , mensaje => string]
     */
    public function createUser(string $name, string $role = 'reader'): array
    {
        // Debe empezar por letras 
        $regex_name = '/^[a-zA-Z][a-zA-Z0-9_-]*$/';

        if (strlen($name) < 3) {
            return [
                'validation error' => 'The username must be more than 3 characters long'
            ];
        }
        if ($role != 'reader' && $role != 'admin') {
            return [
                'validation error' => 'The role must be reader or admin'
            ];
        }

        if (!preg_match($regex_name, $name)) {
            return [
                'validation error' => 'The username must begin with letters.'
            ];
        }

        try {
            $idUser = $this->model->createUser($name, $role);

            if ($idUser) {
                return [
                    'id' => $idUser,
                    'message' => 'User created'
                ];
            }

            return [
                'id' => null,
                'message' => "Failed to create user"
            ];
        } catch (Exception $e) {
            error_log("Error in createUser({$name}, {$role}): " . $e->getMessage());
        }
    }
}
