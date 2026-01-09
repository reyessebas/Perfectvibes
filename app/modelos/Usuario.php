<?php
/**
 * Modelo de Usuario
 * 
 * Representa la entidad Usuario en el sistema
 */

namespace App\Modelos;

use PDO;

class Usuario
{
    private ?int $id = null;
    private string $nombre;
    private string $apellido;
    private string $correo;
    private string $telefono;
    private string $contraseña;
    private string $role;
    private ?\DateTime $fechaCreacion = null;
    
    private PDO $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    // Getters
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNombre(): string
    {
        return $this->nombre;
    }

    public function getApellido(): string
    {
        return $this->apellido;
    }

    public function getCorreo(): string
    {
        return $this->correo;
    }

    public function getTelefono(): string
    {
        return $this->telefono;
    }

    public function getRole(): string
    {
        return $this->role;
    }

    public function getFechaCreacion(): ?\DateTime
    {
        return $this->fechaCreacion;
    }

    // Setters
    public function setId(?int $id): void
    {
        $this->id = $id;
    }

    public function setNombre(string $nombre): void
    {
        $this->nombre = trim($nombre);
    }

    public function setApellido(string $apellido): void
    {
        $this->apellido = trim($apellido);
    }

    public function setCorreo(string $correo): void
    {
        $this->correo = strtolower(trim($correo));
    }

    public function setTelefono(string $telefono): void
    {
        $this->telefono = trim($telefono);
    }

    public function setContraseña(string $contraseña): void
    {
        $this->contraseña = password_hash($contraseña, PASSWORD_DEFAULT);
    }

    public function setRole(string $role): void
    {
        $this->role = $role;
    }

    /**
     * Verificar contraseña
     */
    public function verificarContraseña(string $contraseña): bool
    {
        return password_verify($contraseña, $this->contraseña);
    }

    /**
     * Validar datos del usuario
     */
    public function validar(): array
    {
        $errores = [];

        if (empty($this->nombre)) {
            $errores[] = "El nombre es obligatorio";
        }

        if (empty($this->apellido)) {
            $errores[] = "El apellido es obligatorio";
        }

        if (empty($this->correo) || !filter_var($this->correo, FILTER_VALIDATE_EMAIL)) {
            $errores[] = "El correo electrónico no es válido";
        }

        if (empty($this->telefono)) {
            $errores[] = "El teléfono es obligatorio";
        }

        return $errores;
    }

    /**
     * Guardar usuario en la base de datos
     */
    public function guardar(): bool
    {
        $errores = $this->validar();
        if (!empty($errores)) {
            return false;
        }

        if ($this->id === null) {
            return $this->crear();
        } else {
            return $this->actualizar();
        }
    }

    /**
     * Crear nuevo usuario
     */
    private function crear(): bool
    {
        $sql = "INSERT INTO usuarios (nombre, apellido, correo, telefono, contraseña, role) 
                VALUES (:nombre, :apellido, :correo, :telefono, :contraseña, :role)";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':nombre', $this->nombre);
        $stmt->bindParam(':apellido', $this->apellido);
        $stmt->bindParam(':correo', $this->correo);
        $stmt->bindParam(':telefono', $this->telefono);
        $stmt->bindParam(':contraseña', $this->contraseña);
        $stmt->bindParam(':role', $this->role);

        if ($stmt->execute()) {
            $this->id = (int)$this->db->lastInsertId();
            return true;
        }

        return false;
    }

    /**
     * Actualizar usuario existente
     */
    private function actualizar(): bool
    {
        $sql = "UPDATE usuarios 
                SET nombre = :nombre, apellido = :apellido, correo = :correo, 
                    telefono = :telefono, role = :role
                WHERE id = :id";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id', $this->id, PDO::PARAM_INT);
        $stmt->bindParam(':nombre', $this->nombre);
        $stmt->bindParam(':apellido', $this->apellido);
        $stmt->bindParam(':correo', $this->correo);
        $stmt->bindParam(':telefono', $this->telefono);
        $stmt->bindParam(':role', $this->role);

        return $stmt->execute();
    }

    /**
     * Buscar usuario por ID
     */
    public function buscarPorId(int $id): ?Usuario
    {
        $sql = "SELECT * FROM usuarios WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        $data = $stmt->fetch();
        if ($data) {
            return self::fromArray($data, $this->db);
        }

        return null;
    }

    /**
     * Buscar usuario por correo
     */
    public function buscarPorCorreo(string $correo): ?Usuario
    {
        $sql = "SELECT * FROM usuarios WHERE correo = :correo";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':correo', $correo);
        $stmt->execute();

        $data = $stmt->fetch();
        if ($data) {
            return self::fromArray($data, $this->db);
        }

        return null;
    }

    /**
     * Obtener todos los usuarios
     */
    public function obtenerTodos(): array
    {
        $sql = "SELECT * FROM usuarios ORDER BY fechaCreacion DESC";
        $stmt = $this->db->query($sql);
        
        $usuarios = [];
        while ($data = $stmt->fetch()) {
            $usuarios[] = self::fromArray($data, $this->db);
        }

        return $usuarios;
    }

    /**
     * Eliminar usuario
     */
    public function eliminar(): bool
    {
        if ($this->id === null) {
            return false;
        }

        $sql = "DELETE FROM usuarios WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id', $this->id, PDO::PARAM_INT);

        return $stmt->execute();
    }

    /**
     * Crear instancia desde array
     */
    public static function fromArray(array $data, PDO $db): Usuario
    {
        $usuario = new self($db);
        $usuario->id = (int)$data['id'];
        $usuario->nombre = $data['nombre'];
        $usuario->apellido = $data['apellido'];
        $usuario->correo = $data['correo'];
        $usuario->telefono = $data['telefono'];
        $usuario->contraseña = $data['contraseña'];
        $usuario->role = $data['role'];
        
        if (isset($data['fechaCreacion'])) {
            $usuario->fechaCreacion = new \DateTime($data['fechaCreacion']);
        }

        return $usuario;
    }

    /**
     * Convertir a array
     */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'nombre' => $this->nombre,
            'apellido' => $this->apellido,
            'correo' => $this->correo,
            'telefono' => $this->telefono,
            'role' => $this->role,
            'fechaCreacion' => $this->fechaCreacion?->format('Y-m-d H:i:s')
        ];
    }
}
