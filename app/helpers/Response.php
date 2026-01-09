<?php
/**
 * Clase Helper para redirecciones y respuestas
 */

namespace App\Helpers;

class Response
{
    /**
     * Redireccionar a una URL
     */
    public static function redirect(string $url): void
    {
        header("Location: {$url}");
        exit;
    }

    /**
     * Redireccionar con mensaje flash
     */
    public static function redirectWithMessage(string $url, string $mensaje, string $tipo = 'info'): void
    {
        $_SESSION['flash_mensaje'] = $mensaje;
        $_SESSION['flash_tipo'] = $tipo;
        self::redirect($url);
    }

    /**
     * Devolver respuesta JSON
     */
    public static function json(array $data, int $statusCode = 200): void
    {
        http_response_code($statusCode);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        exit;
    }

    /**
     * Devolver error JSON
     */
    public static function jsonError(string $mensaje, int $statusCode = 400): void
    {
        self::json([
            'exito' => false,
            'mensaje' => $mensaje
        ], $statusCode);
    }

    /**
     * Devolver éxito JSON
     */
    public static function jsonSuccess(string $mensaje, array $data = [], int $statusCode = 200): void
    {
        self::json(array_merge([
            'exito' => true,
            'mensaje' => $mensaje
        ], $data), $statusCode);
    }

    /**
     * Establecer código de estado HTTP
     */
    public static function setStatusCode(int $code): void
    {
        http_response_code($code);
    }

    /**
     * Devolver página 404
     */
    public static function notFound(string $mensaje = 'Página no encontrada'): void
    {
        http_response_code(404);
        include __DIR__ . '/../../vistas/errores/404.php';
        exit;
    }

    /**
     * Devolver error 403 Forbidden
     */
    public static function forbidden(string $mensaje = 'Acceso denegado'): void
    {
        http_response_code(403);
        include __DIR__ . '/../../vistas/errores/403.php';
        exit;
    }

    /**
     * Obtener mensaje flash y eliminarlo
     */
    public static function getFlashMessage(): ?array
    {
        if (isset($_SESSION['flash_mensaje'])) {
            $mensaje = [
                'texto' => $_SESSION['flash_mensaje'],
                'tipo' => $_SESSION['flash_tipo'] ?? 'info'
            ];
            unset($_SESSION['flash_mensaje'], $_SESSION['flash_tipo']);
            return $mensaje;
        }
        return null;
    }

    /**
     * Verificar si es una petición AJAX
     */
    public static function isAjax(): bool
    {
        return !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && 
               strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
    }

    /**
     * Obtener método HTTP de la petición
     */
    public static function getMethod(): string
    {
        return strtoupper($_SERVER['REQUEST_METHOD']);
    }

    /**
     * Verificar si es una petición POST
     */
    public static function isPost(): bool
    {
        return self::getMethod() === 'POST';
    }

    /**
     * Verificar si es una petición GET
     */
    public static function isGet(): bool
    {
        return self::getMethod() === 'GET';
    }
}
