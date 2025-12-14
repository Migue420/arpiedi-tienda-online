// Script para crear el primer usuario administrador
require('dotenv').config();
const bcrypt = require('bcryptjs');
const { db } = require('./database/db');

async function crearPrimerAdmin() {
    const username = 'admin';
    const password = 'arpiedi2024'; // Cambiar después del primer login
    const email = 'admin@arpiedi.com';

    try {
        const hashedPassword = await bcrypt.hash(password, 10);

        db.run(
            'INSERT INTO administradores (username, password, email) VALUES (?, ?, ?)',
            [username, hashedPassword, email],
            function (err) {
                if (err) {
                    if (err.message.includes('UNIQUE')) {
                        console.log('⚠️  El usuario admin ya existe');
                    } else {
                        console.error('❌ Error:', err);
                    }
                } else {
                    console.log(`
╔══════════════════════════════════════╗
║   ✅ Administrador Creado           ║
║                                      ║
║   Usuario: ${username}                    ║
║   Contraseña: ${password}           ║
║   Email: ${email}       ║
║                                      ║
║   ⚠️  IMPORTANTE: Cambia la         ║
║   contraseña después del login      ║
╚══════════════════════════════════════╝
                    `);
                }
                db.close();
            }
        );
    } catch (error) {
        console.error('Error:', error);
        db.close();
    }
}

// Inicializar base de datos primero
const { initDatabase } = require('./database/db');
initDatabase();

// Esperar un momento para que se creen las tablas
setTimeout(crearPrimerAdmin, 1000);
