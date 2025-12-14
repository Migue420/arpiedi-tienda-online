# Arpiedi Backend - Sistema de Gestión

## 🚀 Inicio Rápido

### 1. Instalar Dependencias
```bash
cd backend
npm install
```

### 2. Crear Administrador Inicial
```bash
node setup-admin.js
```

Esto creará un usuario administrador con:
- **Usuario**: admin
- **Contraseña**: arpiedi2024
- **Email**: admin@arpiedi.com

⚠️ **IMPORTANTE**: Cambia la contraseña después del primer login.

### 3. Iniciar el Servidor
```bash
npm start
```

El servidor estará disponible en: `http://localhost:3000`

## 📋 Endpoints API

### Autenticación
- `POST /api/auth/login` - Iniciar sesión
- `GET /api/auth/verificar` - Verificar token (requiere auth)

### Medidas
- `POST /api/medidas` - Crear nueva solicitud (público)
- `GET /api/medidas` - Listar todas (requiere auth)
- `GET /api/medidas/:id` - Ver detalle (requiere auth)
- `PUT /api/medidas/:id/estado` - Actualizar estado (requiere auth)
- `DELETE /api/medidas/:id` - Eliminar (requiere auth)

## 🎛️ Panel de Administración

Accede al panel en: `file:///ruta/completa/admin/index.html`

O abre directamente el archivo `admin/index.html` en tu navegador.

## 📁 Estructura del Proyecto

```
backend/
├── database/
│   ├── db.js              # Configuración SQLite
│   └── arpiedi.db         # Base de datos (se crea automáticamente)
├── routes/
│   ├── auth.js            # Rutas de autenticación
│   └── medidas.js         # Rutas de medidas
├── controllers/
│   ├── authController.js  # Lógica de autenticación
│   └── medidasController.js # Lógica de medidas
├── middleware/
│   └── auth.js            # Middleware de autenticación
├── uploads/               # Carpeta para fotos (se crea automáticamente)
├── server.js              # Servidor principal
├── setup-admin.js         # Script de configuración
└── .env                   # Variables de entorno

admin/
└── index.html             # Panel de administración
```

## 🔒 Seguridad

- Las contraseñas se almacenan con bcrypt (hash)
- Autenticación mediante JWT
- Tokens expiran en 24 horas
- Validación de archivos (solo imágenes, máx 5MB)

## 📝 Notas

- La base de datos SQLite se crea automáticamente al iniciar el servidor
- Los archivos subidos se guardan en `backend/uploads/`
- El panel de administración debe abrirse directamente en el navegador (no requiere servidor web)
