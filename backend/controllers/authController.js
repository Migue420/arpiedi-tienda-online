const { db } = require('../database/db');
const bcrypt = require('bcryptjs');
const jwt = require('jsonwebtoken');

// Login de administrador
exports.login = (req, res) => {
    const { username, password } = req.body;

    if (!username || !password) {
        return res.status(400).json({
            success: false,
            message: 'Usuario y contraseña son requeridos'
        });
    }

    const sql = 'SELECT * FROM administradores WHERE username = ?';

    db.get(sql, [username], async (err, admin) => {
        if (err) {
            console.error('Error en login:', err);
            return res.status(500).json({
                success: false,
                message: 'Error en el servidor'
            });
        }

        if (!admin) {
            return res.status(401).json({
                success: false,
                message: 'Credenciales inválidas'
            });
        }

        // Verificar contraseña
        const passwordMatch = await bcrypt.compare(password, admin.password);

        if (!passwordMatch) {
            return res.status(401).json({
                success: false,
                message: 'Credenciales inválidas'
            });
        }

        // Generar token JWT
        const token = jwt.sign(
            { id: admin.id, username: admin.username },
            process.env.JWT_SECRET,
            { expiresIn: '24h' }
        );

        res.json({
            success: true,
            message: 'Login exitoso',
            token,
            admin: {
                id: admin.id,
                username: admin.username,
                email: admin.email
            }
        });
    });
};

// Crear administrador (solo para setup inicial)
exports.crearAdmin = async (req, res) => {
    const { username, password, email } = req.body;

    if (!username || !password) {
        return res.status(400).json({
            success: false,
            message: 'Usuario y contraseña son requeridos'
        });
    }

    try {
        // Hash de la contraseña
        const hashedPassword = await bcrypt.hash(password, 10);

        const sql = 'INSERT INTO administradores (username, password, email) VALUES (?, ?, ?)';

        db.run(sql, [username, hashedPassword, email || null], function (err) {
            if (err) {
                if (err.message.includes('UNIQUE')) {
                    return res.status(400).json({
                        success: false,
                        message: 'El usuario ya existe'
                    });
                }
                console.error('Error al crear admin:', err);
                return res.status(500).json({
                    success: false,
                    message: 'Error al crear administrador'
                });
            }

            res.status(201).json({
                success: true,
                message: 'Administrador creado correctamente',
                id: this.lastID
            });
        });
    } catch (error) {
        console.error('Error:', error);
        res.status(500).json({
            success: false,
            message: 'Error en el servidor'
        });
    }
};

// Verificar token
exports.verificarToken = (req, res) => {
    res.json({
        success: true,
        message: 'Token válido',
        user: req.user
    });
};
