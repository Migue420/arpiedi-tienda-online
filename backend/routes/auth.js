const express = require('express');
const router = express.Router();
const authController = require('../controllers/authController');
const { verificarAuth } = require('../middleware/auth');

// Rutas de autenticación
router.post('/login', authController.login);
router.post('/crear-admin', authController.crearAdmin); // Solo para setup inicial
router.get('/verificar', verificarAuth, authController.verificarToken);

module.exports = router;
