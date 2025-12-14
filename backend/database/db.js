const sqlite3 = require('sqlite3').verbose();
const path = require('path');

// Crear conexión a la base de datos
const dbPath = path.join(__dirname, 'arpiedi.db');
const db = new sqlite3.Database(dbPath, (err) => {
    if (err) {
        console.error('Error al conectar con la base de datos:', err);
    } else {
        console.log('✅ Conectado a la base de datos SQLite');
    }
});

// Habilitar foreign keys
db.run('PRAGMA foreign_keys = ON');

// Función para inicializar las tablas
const initDatabase = () => {
    // Tabla de administradores
    db.run(`
        CREATE TABLE IF NOT EXISTS administradores (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            username TEXT UNIQUE NOT NULL,
            password TEXT NOT NULL,
            email TEXT,
            fecha_creacion DATETIME DEFAULT CURRENT_TIMESTAMP
        )
    `);

    // Tabla de medidas (PRIORIDAD)
    db.run(`
        CREATE TABLE IF NOT EXISTS medidas (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            cliente_nombre TEXT NOT NULL,
            cliente_email TEXT NOT NULL,
            cliente_telefono TEXT,
            pie_derecho_largo REAL,
            pie_derecho_ancho REAL,
            pie_izquierdo_largo REAL,
            pie_izquierdo_ancho REAL,
            foto_derecha TEXT,
            foto_izquierda TEXT,
            foto_lateral TEXT,
            foto_superior TEXT,
            notas TEXT,
            estado TEXT DEFAULT 'pendiente',
            fecha_creacion DATETIME DEFAULT CURRENT_TIMESTAMP
        )
    `);

    // Tabla de contactos
    db.run(`
        CREATE TABLE IF NOT EXISTS contactos (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            nombre TEXT NOT NULL,
            email TEXT NOT NULL,
            telefono TEXT,
            mensaje TEXT NOT NULL,
            fecha_creacion DATETIME DEFAULT CURRENT_TIMESTAMP,
            leido BOOLEAN DEFAULT 0
        )
    `);

    // Tabla de pedidos
    db.run(`
        CREATE TABLE IF NOT EXISTS pedidos (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            cliente_nombre TEXT NOT NULL,
            cliente_email TEXT NOT NULL,
            cliente_telefono TEXT,
            cliente_direccion TEXT,
            total REAL,
            estado TEXT DEFAULT 'pendiente',
            fecha_creacion DATETIME DEFAULT CURRENT_TIMESTAMP
        )
    `);

    // Tabla de items de pedidos
    db.run(`
        CREATE TABLE IF NOT EXISTS pedidos_items (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            pedido_id INTEGER,
            producto_nombre TEXT NOT NULL,
            cantidad INTEGER NOT NULL,
            precio_unitario REAL,
            FOREIGN KEY (pedido_id) REFERENCES pedidos(id) ON DELETE CASCADE
        )
    `);

    // Tabla de artículos
    db.run(`
        CREATE TABLE IF NOT EXISTS articulos (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            nombre TEXT NOT NULL,
            descripcion TEXT,
            categoria TEXT,
            precio REAL,
            imagen_url TEXT,
            stock INTEGER DEFAULT 0,
            activo BOOLEAN DEFAULT 1,
            fecha_creacion DATETIME DEFAULT CURRENT_TIMESTAMP,
            fecha_actualizacion DATETIME DEFAULT CURRENT_TIMESTAMP
        )
    `);

    console.log('✅ Tablas de base de datos inicializadas');
};

module.exports = { db, initDatabase };
