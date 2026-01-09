<?php
/**
 * Archivo de inicialización de la aplicación
 * 
 * Carga todas las configuraciones necesarias y el autoloader
 */

// Cargar autoloader
require_once __DIR__ . '/../../proveedores/autoload.php';

// Importar clases necesarias
use App\Config\Config;
use App\Config\Database;

// Inicializar configuración de la aplicación
Config::init();

// La conexión a la base de datos se creará cuando sea necesaria
// usando Database::getInstance()

