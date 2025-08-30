# Proyecto PHP - Sistema de Gestión

## Descripción
Sistema de gestión desarrollado en PHP con funcionalidades de autenticación y administración de productos.

## Requisitos del Sistema
- PHP 7.4 o superior
- Servidor web (Apache/Nginx)
- Base de datos MySQL/MariaDB
- Extensión PHP MySQLi habilitada

## Instalación

### 1. Clonar el repositorio
```bash
git clone [URL_DEL_REPOSITORIO]
cd proyectophp
```

### 2. Configurar la base de datos
- Crear una base de datos MySQL
- Copiar `config/database.example.php` a `config/database.php`
- Editar `config/database.php` con tus credenciales de base de datos

### 3. Ejecutar el script de configuración
```bash
php setup_db.php
```

### 4. Configurar el servidor web
- Apuntar el DocumentRoot a la carpeta del proyecto
- Asegurar que PHP esté habilitado

## Estructura del Proyecto
```
proyectophp/
├── admin.php              # Panel de administración
├── api/                   # API REST para productos
├── config/                # Configuración de base de datos
├── index.php              # Página principal
├── login.php              # Sistema de autenticación
├── logout.php             # Cerrar sesión
├── setup_db.php           # Script de configuración inicial
└── uploads/               # Archivos subidos
```

## Uso
1. Acceder a `index.php` para ver la página principal
2. Usar `login.php` para autenticarse
3. Acceder a `admin.php` para administrar productos
4. La API está disponible en `/api/`

## Características
- Sistema de autenticación seguro
- Panel de administración
- API REST para productos
- Gestión de archivos
- Interfaz responsive

## Contribución
1. Fork el proyecto
2. Crea una rama para tu feature (`git checkout -b feature/AmazingFeature`)
3. Commit tus cambios (`git commit -m 'Add some AmazingFeature'`)
4. Push a la rama (`git push origin feature/AmazingFeature`)
5. Abre un Pull Request

## Licencia
Este proyecto está bajo la Licencia MIT.
