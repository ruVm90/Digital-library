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

    // validacion de nombre
    private const MAX_NAME_LENGTH = 50;
    private const MIN_NAME_LENGTH = 3;
    private const PATTERN = '/^[a-zA-Z][a-zA-Z0-9_-]*$/';

    // roles
    private const VALID_ROLES = ['reader', 'admin'];
    private const DEFAULT_ROLE = 'reader';

    /**
     * @param UserModel|null $model Modelo de usuarios (inyección de dependencias)
     */
    public function __construct(?UserModel $model = null)
    {
        $this->model = $model ?? new UserModel();
    }


    /**
     * Funcion helper que devuelve error de validacion
     * @param string mensaje de error de validacion al usuario
     * @return array Estructura: ['status' => int, 'data' => null, 'message' => string]
     */
    private function validationError(string $message): array
    {
        return [
            'status' => 400,
            'message' => $message,
            'data' => null
        ];
    }
    /**
     * Funcion helper que valida el nombre de usuario
     * @param string nombre de usuario
     * @return array devuelve validationError o null si pasa las validaciones
     */
    private function validateName($name): ?array
    {
        $nameLength = strlen($name);

        if ($nameLength < self::MIN_NAME_LENGTH) {
            return $this->validationError(
                "Username must be at least " . self::MIN_NAME_LENGTH . " characters long"
            );
        }
        if ($nameLength > self::MAX_NAME_LENGTH) {
            return $this->validationError(
                "Username must not exceed " . self::MAX_NAME_LENGTH . " characters"
            );
        }
        if (!preg_match(self::PATTERN, $name)) {
            return $this->validationError(
                'The username must begin with letters.'
            );
        }
        return null;
    }
    /**
     * Funcion helper que valida el rol de usuario
     * @param string rol de usuario
     * @return array devuelve validationError o null si pasa las validaciones
     */
    private function validateRole(string $role)
    {
        if (!in_array($role, self::VALID_ROLES, true)) {
            return $this->validationError(
                "Role must be one of: " . implode(', ', self::VALID_ROLES)
            );
        }
        return null;
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
                return $this->validationError('No users found');
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
            return $this->validationError('User ID must be a positive integer');
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
    public function createUser(string $name, string $role = self::DEFAULT_ROLE): array
    {
        $resultValidationName = $this->validateName($name);
        $resultValidationRole = $this->validateRole($role);

        if ($resultValidationName !== null) {
            return $resultValidationName;
        }
        if ($resultValidationRole !== null) {
            return $resultValidationRole;
        }

        try {
            $userId = $this->model->createUser($name, $role);

            if ($userId) {
                return [
                    'status' => 201,
                    'data' => [
                        'name' => $name,
                        'id' => $userId,
                        'role' => $role
                    ],
                    'message' => 'User created successfully'
                ];
            }

            return [
                'status' => 500,
                'data' => null,
                'message' => 'Failed to create user'
            ];
        } catch (Exception $e) {
            // Log del error con información útil
            error_log(sprintf(
                "Error in createUser(name: %s, role: %s): %s",
                $name,
                $role,
                $e->getMessage()
            ));

            return [
                'status' => 500,
                'data' => null,
                'message' => 'Internal server error'
            ];
        }
    }

    /**
     * Actualiza los datos de un usuario y lo almacena en la db
     * 
     * @param string $name nombre nuevo del usuario.
     * @return array [id => int|null , mensaje => string]
     */
    function updateUser(string $newName, int $id, string $role): ?array
    {

        $resultValidateName = $this->validateName($newName);
        $resultValidationRole = $this->validateRole($role);

        if ($resultValidateName !== null) {
            return $resultValidateName;
        }
        if ($resultValidationRole !== null) {
            return $resultValidationRole;
        }

        try {
            $update = $this->model->updateUser($newName, $id, $role);

            if ($update) {
                return [
                    'status' => 200,
                    'data' => [
                        'name' => $newName,
                        'id' => $id,
                        'role' => $role
                    ],
                    'message' => 'User updated successfully.'
                ];
            }
            return [
                'status' => 404,
                'data' => null,
                'message' => 'User not found.'
            ];
        } catch (Exception $e) {
            error_log(sprintf(
                "Error in UpdateUser(name: %s, role: %s): %s",
                $newName,
                $role,
                $e->getMessage()
            ));

            return [
                'status' => 500,
                'data' => null,
                'message' => 'Internal server error'
            ];
        }
    }

     /**
     * Elimina un usuario de la db
     * 
     * @param int id del usuario que se quiere borrar.
     * @return array [id => int|null , mensaje => string]
     */
    function deleteUser(int $id) : array
    {
      if ($id <= 0) {
            return $this->validationError('User ID must be a positive integer');
        }

        try {
            $delete = $this->model->deleteUser($id);

            if ($delete) {
                return [
                    'status' => 200,
                    'message' => 'User deleted successfully'
                ];
            }
            return [
                    'status' => 400,
                    'message' => 'User not found'
                ];
        } catch (Exception $e) {
            error_log("Error in deleteUser({$id}): " . $e->getMessage());

            return [
                'status' => 500,
                'data' => null,
                'message' => 'Internal server error'
            ];
        }
    }
}
