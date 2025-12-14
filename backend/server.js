require('dotenv').config();
const express = require('express');
const cors = require('cors');
const path = require('path');
const { initDatabase } = require('./database/db');

const app = express();
const PORT = process.env.PORT || 3000;

// Middleware
app.use(cors());
app.use(express.json());
app.use(express.urlencoded({ extended: true }));

// Servir archivos estáticos
app.use('/uploads', express.static(path.join(__dirname, 'uploads')));

// Inicializar base de datos
initDatabase();

// Rutas
const authRoutes = require('./routes/auth');
const medidasRoutes = require('./routes/medidas');

app.use('/api/auth', authRoutes);
app.use('/api/medidas', medidasRoutes);

// Ruta de prueba
app.get('/api/health', (req, res) => {
    res.json({
        success: true,
        message: 'Servidor funcionando correctamente',
        timestamp: new Date().toISOString()
    });
});

// Manejo de errores
app.use((err, req, res, next) => {
    console.error('Error:', err);
    res.status(500).json({
        success: false,
        message: err.message || 'Error en el servidor'
    });
});

// Iniciar servidor
app.listen(PORT, () => {
    console.log(`
╔══════════════════════════════════════╗
║   🚀 Servidor Arpiedi Backend       ║
║   📡 Puerto: ${PORT}                    ║
║   🌐 http://localhost:${PORT}          ║
║   ✅ Estado: Activo                  ║
╚══════════════════════════════════════╝
    `);
});

module.exports = app;
