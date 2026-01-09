<?php
/**
 * Controlador de Usuarios
 * 
 * Maneja las acciones relacionadas con usuarios
 */

namespace App\Controladores;

use App\Modelos\Usuario;
use App\Config\Database;
use PDO;

class ControladorUsuario
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    /**
     * Registrar nuevo usuario
     */
    public function registrar(array $datos): array
    {
        $resultado = ['exito' => false, 'mensaje' => '', 'errores' => []];

        try {
            // Validar que el correo no exista
            $usuario = new Usuario($this->db);
            $usuarioExistente = $usuario->buscarPorCorreo($datos['correo']);
            
            if ($usuarioExistente !== null) {
                $resultado['mensaje'] = 'El correo electrónico ya está registrado';
                return $resultado;
            }

            // Crear nuevo usuario
            $nuevoUsuario = new Usuario($this->db);
            $nuevoUsuario->setNombre($datos['nombre']);
            $nuevoUsuario->setApellido($datos['apellido']);
            $nuevoUsuario->setCorreo($datos['correo']);
            $nuevoUsuario->setTelefono($datos['telefono']);
            $nuevoUsuario->setContraseña($datos['contraseña']);
            $nuevoUsuario->setRole($datos['role'] ?? 'user');

            // Validar datos
            $errores = $nuevoUsuario->validar();
            if (!empty($errores)) {
                $resultado['errores'] = $errores;
                $resultado['mensaje'] = 'Errores de validación';
                return $resultado;
            }

            // Guardar
            if ($nuevoUsuario->guardar()) {
                $resultado['exito'] = true;
                $resultado['mensaje'] = 'Usuario registrado exitosamente';
                $resultado['usuario'] = $nuevoUsuario->toArray();
            } else {
                $resultado['mensaje'] = 'Error al guardar el usuario';
            }

        } catch (\Exception $e) {
            $resultado['mensaje'] = 'Error del sistema: ' . $e->getMessage();
            error_log("Error en registro de usuario: " . $e->getMessage());
        }

        return $resultado;
    }

    /**
     * Iniciar sesión
     */
    public function login(string $correo, string $contraseña): array
    {
        $resultado = ['exito' => false, 'mensaje' => '', 'usuario' => null];

        try {
            $usuario = new Usuario($this->db);
            $usuarioEncontrado = $usuario->buscarPorCorreo($correo);

            if ($usuarioEncontrado === null) {
                $resultado['mensaje'] = 'Credenciales incorrectas';
                return $resultado;
            }

            if (!$usuarioEncontrado->verificarContraseña($contraseña)) {
                $resultado['mensaje'] = 'Credenciales incorrectas';
                return $resultado;
            }

            // Iniciar sesión
            $_SESSION['usuario_id'] = $usuarioEncontrado->getId();
            $_SESSION['usuario_nombre'] = $usuarioEncontrado->getNombre();
            $_SESSION['usuario_correo'] = $usuarioEncontrado->getCorreo();
            $_SESSION['role'] = $usuarioEncontrado->getRole();

            $resultado['exito'] = true;
            $resultado['mensaje'] = 'Inicio de sesión exitoso';
            $resultado['usuario'] = $usuarioEncontrado->toArray();

        } catch (\Exception $e) {
            $resultado['mensaje'] = 'Error del sistema';
            error_log("Error en login: " . $e->getMessage());
        }

        return $resultado;
    }

    /**
     * Cerrar sesión
     */
    public function logout(): void
    {
        session_unset();
        session_destroy();
    }

    /**
     * Obtener usuario actual
     */
    public function usuarioActual(): ?Usuario
    {
        if (!isset($_SESSION['usuario_id'])) {
            return null;
        }

        $usuario = new Usuario($this->db);
        return $usuario->buscarPorId($_SESSION['usuario_id']);
    }

    /**
     * Verificar si el usuario está autenticado
     */
    public function estaAutenticado(): bool
    {
        return isset($_SESSION['usuario_id']);
    }

    /**
     * Verificar si el usuario es administrador
     */
    public function esAdmin(): bool
    {
        return isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
    }

    /**
     * Actualizar usuario
     */
    public function actualizar(int $id, array $datos): array
    {
        $resultado = ['exito' => false, 'mensaje' => ''];

        try {
            $usuario = new Usuario($this->db);
            $usuarioExistente = $usuario->buscarPorId($id);

            if ($usuarioExistente === null) {
                $resultado['mensaje'] = 'Usuario no encontrado';
                return $resultado;
            }

            // Actualizar datos
            if (isset($datos['nombre'])) {
                $usuarioExistente->setNombre($datos['nombre']);
            }
            if (isset($datos['apellido'])) {
                $usuarioExistente->setApellido($datos['apellido']);
            }
            if (isset($datos['correo'])) {
                $usuarioExistente->setCorreo($datos['correo']);
            }
            if (isset($datos['telefono'])) {
                $usuarioExistente->setTelefono($datos['telefono']);
            }
            if (isset($datos['contraseña']) && !empty($datos['contraseña'])) {
                $usuarioExistente->setContraseña($datos['contraseña']);
            }
            if (isset($datos['role'])) {
                $usuarioExistente->setRole($datos['role']);
            }

            if ($usuarioExistente->guardar()) {
                $resultado['exito'] = true;
                $resultado['mensaje'] = 'Usuario actualizado exitosamente';
            } else {
                $resultado['mensaje'] = 'Error al actualizar el usuario';
            }

        } catch (\Exception $e) {
            $resultado['mensaje'] = 'Error del sistema';
            error_log("Error al actualizar usuario: " . $e->getMessage());
        }

        return $resultado;
    }

    /**
     * Eliminar usuario
     */
    public function eliminar(int $id): array
    {
        $resultado = ['exito' => false, 'mensaje' => ''];

        try {
            $usuario = new Usuario($this->db);
            $usuarioExistente = $usuario->buscarPorId($id);

            if ($usuarioExistente === null) {
                $resultado['mensaje'] = 'Usuario no encontrado';
                return $resultado;
            }

            if ($usuarioExistente->eliminar()) {
                $resultado['exito'] = true;
                $resultado['mensaje'] = 'Usuario eliminado exitosamente';
            } else {
                $resultado['mensaje'] = 'Error al eliminar el usuario';
            }

        } catch (\Exception $e) {
            $resultado['mensaje'] = 'Error del sistema';
            error_log("Error al eliminar usuario: " . $e->getMessage());
        }

        return $resultado;
    }

    /**
     * Listar todos los usuarios
     */
    public function listarTodos(): array
    {
        try {
            $usuario = new Usuario($this->db);
            return $usuario->obtenerTodos();
        } catch (\Exception $e) {
            error_log("Error al listar usuarios: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Buscar usuario por ID
     */
    public function buscarPorId(int $id): ?Usuario
    {
        try {
            $usuario = new Usuario($this->db);
            return $usuario->buscarPorId($id);
        } catch (\Exception $e) {
            error_log("Error al buscar usuario: " . $e->getMessage());
            return null;
        }
    }
}
