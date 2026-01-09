<?php
/**
 * Clase de Utilidades de Seguridad
 * 
 * Proporciona funciones para sanitización y validación de datos
 */

namespace App\Helpers;

class Security
{
    /**
     * Sanitizar cadena de texto
     */
    public static function sanitizeString(string $string): string
    {
        return htmlspecialchars(trim($string), ENT_QUOTES, 'UTF-8');
    }

    /**
     * Sanitizar email
     */
    public static function sanitizeEmail(string $email): string
    {
        return filter_var(trim($email), FILTER_SANITIZE_EMAIL);
    }

    /**
     * Validar email
     */
    public static function validateEmail(string $email): bool
    {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }

    /**
     * Sanitizar número entero
     */
    public static function sanitizeInt($value): int
    {
        return (int)filter_var($value, FILTER_SANITIZE_NUMBER_INT);
    }

    /**
     * Sanitizar número flotante
     */
    public static function sanitizeFloat($value): float
    {
        return (float)filter_var($value, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
    }

    /**
     * Sanitizar URL
     */
    public static function sanitizeUrl(string $url): string
    {
        return filter_var($url, FILTER_SANITIZE_URL);
    }

    /**
     * Generar token CSRF
     */
    public static function generateCsrfToken(): string
    {
        if (!isset($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }

    /**
     * Verificar token CSRF
     */
    public static function verifyCsrfToken(string $token): bool
    {
        return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
    }

    /**
     * Generar hash seguro de contraseña
     */
    public static function hashPassword(string $password): string
    {
        return password_hash($password, PASSWORD_DEFAULT);
    }

    /**
     * Verificar contraseña
     */
    public static function verifyPassword(string $password, string $hash): bool
    {
        return password_verify($password, $hash);
    }

    /**
     * Validar longitud de contraseña
     */
    public static function validatePasswordStrength(string $password, int $minLength = 8): array
    {
        $errores = [];

        if (strlen($password) < $minLength) {
            $errores[] = "La contraseña debe tener al menos {$minLength} caracteres";
        }

        if (!preg_match('/[A-Z]/', $password)) {
            $errores[] = "La contraseña debe contener al menos una letra mayúscula";
        }

        if (!preg_match('/[a-z]/', $password)) {
            $errores[] = "La contraseña debe contener al menos una letra minúscula";
        }

        if (!preg_match('/[0-9]/', $password)) {
            $errores[] = "La contraseña debe contener al menos un número";
        }

        return $errores;
    }

    /**
     * Escapar salida HTML
     */
    public static function escape(?string $string): string
    {
        if ($string === null) {
            return '';
        }
        return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
    }

    /**
     * Proteger contra XSS en arrays
     */
    public static function sanitizeArray(array $data): array
    {
        $sanitized = [];
        foreach ($data as $key => $value) {
            if (is_array($value)) {
                $sanitized[$key] = self::sanitizeArray($value);
            } else {
                $sanitized[$key] = self::sanitizeString($value);
            }
        }
        return $sanitized;
    }

    /**
     * Generar token aleatorio
     */
    public static function generateRandomToken(int $length = 32): string
    {
        return bin2hex(random_bytes($length));
    }

    /**
     * Validar que una cadena contenga solo letras
     */
    public static function validateAlpha(string $string): bool
    {
        return preg_match('/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/', $string) === 1;
    }

    /**
     * Validar que una cadena contenga solo letras y números
     */
    public static function validateAlphanumeric(string $string): bool
    {
        return preg_match('/^[a-zA-Z0-9]+$/', $string) === 1;
    }

    /**
     * Validar número de teléfono (formato colombiano)
     */
    public static function validatePhone(string $phone): bool
    {
        // Acepta formatos: 3001234567, 300-123-4567, (300) 123-4567
        return preg_match('/^[\+]?[(]?[0-9]{3}[)]?[-\s\.]?[0-9]{3}[-\s\.]?[0-9]{4,6}$/', $phone) === 1;
    }
}
