# Arpiedi Backend - PHP + MySQL

Sistema de gestión de medidas con backend PHP y base de datos MySQL.

---

## 🚀 Instalación

### 1. Requisitos Previos

- **PHP 7.4+** instalado
- **MySQL 5.7+** o **MariaDB**
- **Apache** o **Nginx** con mod_rewrite habilitado
- Extensiones PHP: PDO, PDO_MySQL, JSON, mbstring

### 2. Configurar Base de Datos

```bash
# Acceder a MySQL
mysql -u root -p

# Ejecutar el script de instalación
mysql -u root -p < database.sql
```

Esto creará:
- Base de datos `arpiedi_db`
- Todas las tablas necesarias
- Usuario administrador por defecto

### 3. Configurar PHP

Edita `api/config/config.php` y ajusta las credenciales de MySQL:

```php
define('DB_HOST', 'localhost');
define('DB_NAME', 'arpiedi_db');
define('DB_USER', 'root');        // Tu usuario MySQL
define('DB_PASS', '');            // Tu contraseña MySQL
```

### 4. Configurar Permisos

```bash
# Dar permisos de escritura a la carpeta uploads
chmod 777 api/uploads
```

### 5. Configurar Servidor Web

#### Apache
El archivo `.htaccess` ya está configurado. Solo asegúrate de que `mod_rewrite` esté habilitado:

```bash
sudo a2enmod rewrite
sudo service apache2 restart
```

#### Nginx
Añade esta configuración a tu sitio:

```nginx
location /api/ {
    try_files $uri $uri/ /api/index.php?$query_string;
}
```

---

## 📋 Credenciales por Defecto

**Usuario administrador:**
- Usuario: `admin`
- Contraseña: `arpiedi2024`
- Email: `admin@arpiedi.com`

⚠️ **IMPORTANTE**: Cambia la contraseña después del primer login.

---

## 🌐 Endpoints API

### Autenticación

**Login**
```
POST /api/auth/login.php
Body: { "username": "admin", "password": "arpiedi2024" }
Response: { "success": true, "token": "...", "admin": {...} }
```

**Crear Administrador**
```
POST /api/auth/crear-admin.php
Body: { "username": "nuevo", "password": "pass123", "email": "email@example.com" }
```

### Medidas

**Crear Medida** (público)
```
POST /api/medidas/crear.php
Content-Type: multipart/form-data
Body: FormData con campos y fotos
```

**Listar Medidas** (requiere auth)
```
GET /api/medidas/listar.php
Headers: Authorization: Bearer {token}
```

**Ver Detalle** (requiere auth)
```
GET /api/medidas/detalle.php?id=1
Headers: Authorization: Bearer {token}
```

**Actualizar Estado** (requiere auth)
```
PUT /api/medidas/actualizar-estado.php?id=1
Headers: Authorization: Bearer {token}
Body: { "estado": "procesando" }
```

**Eliminar** (requiere auth)
```
DELETE /api/medidas/eliminar.php?id=1
Headers: Authorization: Bearer {token}
```

---

## 📁 Estructura del Proyecto

```
api/
├── config/
│   ├── config.php          # Configuración general
│   ├── database.php        # Conexión MySQL con PDO
│   └── jwt.php             # Implementación JWT pura
├── models/
│   ├── Medida.php          # Modelo de medidas
│   └── Admin.php           # Modelo de administradores
├── middleware/
│   └── auth.php            # Verificación de autenticación
├── auth/
│   ├── login.php           # Endpoint de login
│   └── crear-admin.php     # Crear administrador
├── medidas/
│   ├── crear.php           # Crear medida
│   ├── listar.php          # Listar medidas
│   ├── detalle.php         # Ver detalle
│   ├── actualizar-estado.php  # Cambiar estado
│   └── eliminar.php        # Eliminar medida
├── uploads/                # Fotos subidas
└── .htaccess              # Configuración Apache

database.sql               # Script de instalación MySQL
solicitud-medida.html      # Formulario público
admin/index.html           # Panel de administración
```

---

## 🔒 Seguridad

✅ **Contraseñas hasheadas** con `password_hash()` (bcrypt)  
✅ **JWT personalizado** sin dependencias externas  
✅ **PDO con prepared statements** contra SQL injection  
✅ **Validación de archivos** (tipo y tamaño)  
✅ **CORS configurado** para peticiones seguras  
✅ **Tokens con expiración** de 24 horas  

---

## 🎯 Uso

### 1. Acceder al Formulario de Medidas

Abre en tu navegador:
```
http://localhost/arpiedi-tienda-online-main/solicitud-medida.html
```

### 2. Acceder al Panel Admin

Abre en tu navegador:
```
http://localhost/arpiedi-tienda-online-main/admin/index.html
```

Login con las credenciales por defecto.

---

## 🐛 Solución de Problemas

### Error de conexión a MySQL
- Verifica que MySQL esté corriendo
- Comprueba las credenciales en `api/config/config.php`
- Asegúrate de que la base de datos `arpiedi_db` existe

### Error 500 al subir fotos
- Verifica permisos de la carpeta `api/uploads/`
- Comprueba el tamaño máximo de subida en `php.ini`

### CORS errors
- Asegúrate de que `api/config/config.php` se incluye en todos los endpoints
- Verifica que los headers CORS estén configurados

---

## 📝 Notas

- Este backend usa **PHP puro** sin Composer ni dependencias externas
- Compatible con **hosting compartido** económico
- La implementación JWT es personalizada y segura
- Los archivos se guardan con nombres únicos para evitar conflictos

---

## 🔄 Migración desde Node.js

Si vienes del backend Node.js anterior:

1. Los datos de SQLite NO se migran automáticamente
2. Debes crear un nuevo administrador
3. Las URLs de la API han cambiado (ya actualizadas en el frontend)
4. El puerto 3000 ya no se usa (ahora usa Apache/Nginx en puerto 80/443)

---

## 📞 Soporte

Para problemas o preguntas, revisa:
- Logs de Apache: `/var/log/apache2/error.log`
- Logs de PHP: Configurados en `php.ini`
- Consola del navegador para errores de frontend
