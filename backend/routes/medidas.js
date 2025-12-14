const express = require('express');
const router = express.Router();
const multer = require('multer');
const path = require('path');
const medidasController = require('../controllers/medidasController');
const { verificarAuth } = require('../middleware/auth');

// Configurar multer para subida de archivos
const storage = multer.diskStorage({
    destination: (req, file, cb) => {
        cb(null, 'uploads/');
    },
    filename: (req, file, cb) => {
        const uniqueSuffix = Date.now() + '-' + Math.round(Math.random() * 1E9);
        cb(null, 'medida-' + uniqueSuffix + path.extname(file.originalname));
    }
});

const upload = multer({
    storage: storage,
    limits: { fileSize: 5 * 1024 * 1024 }, // 5MB máximo
    fileFilter: (req, file, cb) => {
        const allowedTypes = /jpeg|jpg|png|gif/;
        const extname = allowedTypes.test(path.extname(file.originalname).toLowerCase());
        const mimetype = allowedTypes.test(file.mimetype);

        if (extname && mimetype) {
            return cb(null, true);
        } else {
            cb(new Error('Solo se permiten imágenes (jpeg, jpg, png, gif)'));
        }
    }
});

// Rutas públicas
router.post('/', upload.fields([
    { name: 'foto_derecha', maxCount: 1 },
    { name: 'foto_izquierda', maxCount: 1 },
    { name: 'foto_lateral', maxCount: 1 },
    { name: 'foto_superior', maxCount: 1 }
]), medidasController.crearMedida);

// Rutas protegidas (requieren autenticación)
router.get('/', verificarAuth, medidasController.obtenerMedidas);
router.get('/:id', verificarAuth, medidasController.obtenerMedidaPorId);
router.put('/:id/estado', verificarAuth, medidasController.actualizarEstadoMedida);
router.delete('/:id', verificarAuth, medidasController.eliminarMedida);

module.exports = router;
