<?php
/**
 * Configuración de Base de Datos
 * 
 * Este archivo maneja la conexión a la base de datos usando PDO
 * y variables de entorno para mayor seguridad
 */

namespace App\Config;

use PDO;
use PDOException;

class Database
{
    private static ?PDO $instance = null;
    private static array $config = [];

    /**
     * Inicializar configuración desde archivo .env
     */
    public static function loadEnv(): void
    {
        $envFile = __DIR__ . '/../../.env';
        
        if (file_exists($envFile)) {
            $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
            foreach ($lines as $line) {
                if (strpos(trim($line), '#') === 0) {
                    continue;
                }
                
                list($key, $value) = explode('=', $line, 2);
                self::$config[trim($key)] = trim($value);
            }
        } else {
            // Valores por defecto si no existe .env
            self::$config = [
                'DB_HOST' => 'localhost',
                'DB_NAME' => 'perfect_vides',
                'DB_USER' => 'root',
                'DB_PASS' => '',
                'DB_CHARSET' => 'utf8mb4'
            ];
        }
    }

    /**
     * Obtener instancia única de la conexión (Singleton)
     */
    public static function getInstance(): PDO
    {
        if (self::$instance === null) {
            self::loadEnv();
            
            $host = self::$config['DB_HOST'] ?? 'localhost';
            $db = self::$config['DB_NAME'] ?? 'perfect_vides';
            $user = self::$config['DB_USER'] ?? 'root';
            $pass = self::$config['DB_PASS'] ?? '';
            $charset = self::$config['DB_CHARSET'] ?? 'utf8mb4';

            $dsn = "mysql:host={$host};dbname={$db};charset={$charset}";
            $options = [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
                PDO::ATTR_PERSISTENT         => false,
            ];

            try {
                self::$instance = new PDO($dsn, $user, $pass, $options);
            } catch (PDOException $e) {
                error_log("Error de conexión a la base de datos: " . $e->getMessage());
                throw new PDOException("Error al conectar con la base de datos. Por favor, contacte al administrador.");
            }
        }

        return self::$instance;
    }

    /**
     * Prevenir clonación del objeto
     */
    private function __clone() {}

    /**
     * Prevenir deserialización
     */
    public function __wakeup()
    {
        throw new \Exception("No se puede deserializar un singleton.");
    }
}
