<?php
/**
 * Archivo de configuración general de la aplicación
 */

namespace App\Config;

class Config
{
    private static array $settings = [];

    /**
     * Inicializar configuración
     */
    public static function init(): void
    {
        // Cargar variables de entorno
        self::loadEnv();

        // Configuración de zona horaria
        date_default_timezone_set('America/Bogota');

        // Configuración de errores según entorno
        $appEnv = self::get('APP_ENV', 'production');
        if ($appEnv === 'development') {
            error_reporting(E_ALL);
            ini_set('display_errors', '1');
        } else {
            error_reporting(0);
            ini_set('display_errors', '0');
        }

        // Configuración de sesión
        if (session_status() === PHP_SESSION_NONE) {
            ini_set('session.cookie_httponly', '1');
            ini_set('session.use_only_cookies', '1');
            ini_set('session.cookie_secure', '0'); // Cambiar a '1' en producción con HTTPS
            session_start();
        }
    }

    /**
     * Cargar variables de entorno desde archivo .env
     */
    private static function loadEnv(): void
    {
        $envFile = __DIR__ . '/../../.env';
        
        if (file_exists($envFile)) {
            $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
            foreach ($lines as $line) {
                if (strpos(trim($line), '#') === 0) {
                    continue;
                }
                
                if (strpos($line, '=') !== false) {
                    list($key, $value) = explode('=', $line, 2);
                    self::$settings[trim($key)] = trim($value);
                }
            }
        }
    }

    /**
     * Obtener valor de configuración
     */
    public static function get(string $key, $default = null)
    {
        return self::$settings[$key] ?? $default;
    }

    /**
     * Establecer valor de configuración
     */
    public static function set(string $key, $value): void
    {
        self::$settings[$key] = $value;
    }

    /**
     * Verificar si existe una configuración
     */
    public static function has(string $key): bool
    {
        return isset(self::$settings[$key]);
    }
}
