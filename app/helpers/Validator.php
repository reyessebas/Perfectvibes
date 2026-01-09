<?php
/**
 * Clase de Utilidades de Validación
 * 
 * Proporciona métodos para validar diferentes tipos de datos
 */

namespace App\Helpers;

class Validator
{
    private array $errores = [];
    private array $datos = [];

    public function __construct(array $datos)
    {
        $this->datos = $datos;
    }

    /**
     * Validar campo requerido
     */
    public function requerido(string $campo, string $mensaje = null): self
    {
        if (!isset($this->datos[$campo]) || empty(trim($this->datos[$campo]))) {
            $this->errores[$campo][] = $mensaje ?? "El campo {$campo} es obligatorio";
        }
        return $this;
    }

    /**
     * Validar email
     */
    public function email(string $campo, string $mensaje = null): self
    {
        if (isset($this->datos[$campo]) && !empty($this->datos[$campo])) {
            if (!filter_var($this->datos[$campo], FILTER_VALIDATE_EMAIL)) {
                $this->errores[$campo][] = $mensaje ?? "El campo {$campo} debe ser un email válido";
            }
        }
        return $this;
    }

    /**
     * Validar longitud mínima
     */
    public function min(string $campo, int $longitud, string $mensaje = null): self
    {
        if (isset($this->datos[$campo]) && strlen($this->datos[$campo]) < $longitud) {
            $this->errores[$campo][] = $mensaje ?? "El campo {$campo} debe tener al menos {$longitud} caracteres";
        }
        return $this;
    }

    /**
     * Validar longitud máxima
     */
    public function max(string $campo, int $longitud, string $mensaje = null): self
    {
        if (isset($this->datos[$campo]) && strlen($this->datos[$campo]) > $longitud) {
            $this->errores[$campo][] = $mensaje ?? "El campo {$campo} no puede tener más de {$longitud} caracteres";
        }
        return $this;
    }

    /**
     * Validar que dos campos coincidan
     */
    public function coincide(string $campo, string $campoComparar, string $mensaje = null): self
    {
        if (isset($this->datos[$campo]) && isset($this->datos[$campoComparar])) {
            if ($this->datos[$campo] !== $this->datos[$campoComparar]) {
                $this->errores[$campo][] = $mensaje ?? "Los campos {$campo} y {$campoComparar} no coinciden";
            }
        }
        return $this;
    }

    /**
     * Validar número
     */
    public function numerico(string $campo, string $mensaje = null): self
    {
        if (isset($this->datos[$campo]) && !is_numeric($this->datos[$campo])) {
            $this->errores[$campo][] = $mensaje ?? "El campo {$campo} debe ser numérico";
        }
        return $this;
    }

    /**
     * Validar patrón regex
     */
    public function patron(string $campo, string $patron, string $mensaje = null): self
    {
        if (isset($this->datos[$campo]) && !preg_match($patron, $this->datos[$campo])) {
            $this->errores[$campo][] = $mensaje ?? "El campo {$campo} no tiene el formato correcto";
        }
        return $this;
    }

    /**
     * Validar URL
     */
    public function url(string $campo, string $mensaje = null): self
    {
        if (isset($this->datos[$campo]) && !filter_var($this->datos[$campo], FILTER_VALIDATE_URL)) {
            $this->errores[$campo][] = $mensaje ?? "El campo {$campo} debe ser una URL válida";
        }
        return $this;
    }

    /**
     * Validar que el valor esté en una lista
     */
    public function enLista(string $campo, array $lista, string $mensaje = null): self
    {
        if (isset($this->datos[$campo]) && !in_array($this->datos[$campo], $lista)) {
            $this->errores[$campo][] = $mensaje ?? "El campo {$campo} contiene un valor no válido";
        }
        return $this;
    }

    /**
     * Verificar si la validación pasó
     */
    public function pasa(): bool
    {
        return empty($this->errores);
    }

    /**
     * Obtener errores
     */
    public function errores(): array
    {
        return $this->errores;
    }

    /**
     * Obtener primer error
     */
    public function primerError(): ?string
    {
        if (empty($this->errores)) {
            return null;
        }

        $primerCampo = array_key_first($this->errores);
        return $this->errores[$primerCampo][0] ?? null;
    }

    /**
     * Obtener datos validados
     */
    public function datos(): array
    {
        return $this->datos;
    }
}
