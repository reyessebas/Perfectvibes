# Perfect Vibes - Sistema de Gestión de Spa

Perfect Vibes es una aplicación web para la gestión de un spa de uñas que incluye:
- Catálogo de productos de belleza
- Sistema de reservas de servicios
- Carrito de compras
- Panel de administración
- Gestión de usuarios

## 📋 Requisitos del Sistema

- PHP 7.4 o superior
- MySQL 5.7 o superior
- Servidor web (Apache/Nginx)
- Composer (opcional, para futuras dependencias)

## 🚀 Instalación

1. **Clonar o descargar el proyecto**
   ```bash
   git clone [url-del-repositorio]
   cd perfect_vibes-main/project-root
   ```

2. **Configurar la base de datos**
   - Crear una base de datos MySQL llamada `perfect_vides`
   - Importar el archivo SQL:
     ```bash
     mysql -u root -p perfect_vides < sql/perfect_vides.sql
     ```

3. **Configurar variables de entorno**
   - Copiar `.env.example` a `.env`
   - Editar `.env` con tus credenciales:
     ```bash
     cp .env.example .env
     ```

4. **Configurar permisos** (en sistemas Unix/Linux)
   ```bash
   chmod -R 755 public/
   chmod -R 777 public/imagenes/
   ```

5. **Acceder a la aplicación**
   - Abrir en el navegador: `http://localhost/project-root/public/`

## 📁 Estructura del Proyecto

```
project-root/
├── app/
│   ├── controladores/      # Controladores de la aplicación
│   ├── modelos/            # Modelos de datos
│   └── servicios/          # Lógica de negocio (nuevo)
├── configuracion/          # Archivos de configuración
├── public/                 # Punto de entrada público
│   ├── css/
│   ├── js/
│   └── imagenes/
├── sql/                    # Scripts de base de datos
├── vistas/                 # Plantillas y vistas
└── vendor/                 # Dependencias (futuro)
```

## 👤 Credenciales por Defecto

**Administrador:**
- Email: admin@perfectvibes.com
- Contraseña: admin123

**Usuario de prueba:**
- Email: user@example.com
- Contraseña: user123

## 🔧 Características

- ✅ Sistema MVC (Modelo-Vista-Controlador)
- ✅ Autoloader PSR-4
- ✅ Seguridad con prepared statements
- ✅ Validación de datos
- ✅ Gestión de sesiones segura
- ✅ Responsive design con Bootstrap 5
- ✅ Panel de administración completo

## 📝 Uso

### Para Clientes:
1. Registrarse o iniciar sesión
2. Explorar productos en el catálogo
3. Agregar productos al carrito
4. Reservar servicios del spa
5. Completar compras

### Para Administradores:
1. Iniciar sesión con cuenta de administrador
2. Acceder al panel de administración
3. Gestionar productos, servicios y usuarios
4. Ver y procesar pedidos y reservas

## 🛡️ Seguridad

- Contraseñas hasheadas con `password_hash()`
- Prepared statements para prevenir SQL injection
- Sanitización de datos de entrada
- Protección CSRF (a implementar)
- Validación de sesiones

## 🤝 Contribuir

1. Fork el proyecto
2. Crear una rama para tu feature (`git checkout -b feature/AmazingFeature`)
3. Commit tus cambios (`git commit -m 'Add some AmazingFeature'`)
4. Push a la rama (`git push origin feature/AmazingFeature`)
5. Abrir un Pull Request

## 📄 Licencia

Este proyecto es de código abierto para fines educativos.

## 📧 Contacto

Perfect Vibes - La Vega, Cundinamarca, Colombia
