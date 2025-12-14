const { db } = require('../database/db');
const path = require('path');
const fs = require('fs');

// Crear nueva solicitud de medidas
exports.crearMedida = (req, res) => {
    const {
        cliente_nombre,
        cliente_email,
        cliente_telefono,
        pie_derecho_largo,
        pie_derecho_ancho,
        pie_izquierdo_largo,
        pie_izquierdo_ancho,
        notas
    } = req.body;

    // Procesar archivos subidos
    const fotos = {};
    if (req.files) {
        if (req.files.foto_derecha) fotos.foto_derecha = req.files.foto_derecha[0].filename;
        if (req.files.foto_izquierda) fotos.foto_izquierda = req.files.foto_izquierda[0].filename;
        if (req.files.foto_lateral) fotos.foto_lateral = req.files.foto_lateral[0].filename;
        if (req.files.foto_superior) fotos.foto_superior = req.files.foto_superior[0].filename;
    }

    const sql = `
        INSERT INTO medidas (
            cliente_nombre, cliente_email, cliente_telefono,
            pie_derecho_largo, pie_derecho_ancho,
            pie_izquierdo_largo, pie_izquierdo_ancho,
            foto_derecha, foto_izquierda, foto_lateral, foto_superior,
            notas
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
    `;

    const params = [
        cliente_nombre,
        cliente_email,
        cliente_telefono || null,
        pie_derecho_largo || null,
        pie_derecho_ancho || null,
        pie_izquierdo_largo || null,
        pie_izquierdo_ancho || null,
        fotos.foto_derecha || null,
        fotos.foto_izquierda || null,
        fotos.foto_lateral || null,
        fotos.foto_superior || null,
        notas || null
    ];

    db.run(sql, params, function (err) {
        if (err) {
            console.error('Error al guardar medidas:', err);
            return res.status(500).json({
                success: false,
                message: 'Error al guardar las medidas'
            });
        }

        res.status(201).json({
            success: true,
            message: 'Medidas guardadas correctamente',
            id: this.lastID
        });
    });
};

// Obtener todas las medidas
exports.obtenerMedidas = (req, res) => {
    const sql = 'SELECT * FROM medidas ORDER BY fecha_creacion DESC';

    db.all(sql, [], (err, rows) => {
        if (err) {
            console.error('Error al obtener medidas:', err);
            return res.status(500).json({
                success: false,
                message: 'Error al obtener las medidas'
            });
        }

        res.json({
            success: true,
            data: rows
        });
    });
};

// Obtener una medida específica
exports.obtenerMedidaPorId = (req, res) => {
    const { id } = req.params;
    const sql = 'SELECT * FROM medidas WHERE id = ?';

    db.get(sql, [id], (err, row) => {
        if (err) {
            console.error('Error al obtener medida:', err);
            return res.status(500).json({
                success: false,
                message: 'Error al obtener la medida'
            });
        }

        if (!row) {
            return res.status(404).json({
                success: false,
                message: 'Medida no encontrada'
            });
        }

        res.json({
            success: true,
            data: row
        });
    });
};

// Actualizar estado de medida
exports.actualizarEstadoMedida = (req, res) => {
    const { id } = req.params;
    const { estado } = req.body;

    const sql = 'UPDATE medidas SET estado = ? WHERE id = ?';

    db.run(sql, [estado, id], function (err) {
        if (err) {
            console.error('Error al actualizar estado:', err);
            return res.status(500).json({
                success: false,
                message: 'Error al actualizar el estado'
            });
        }

        if (this.changes === 0) {
            return res.status(404).json({
                success: false,
                message: 'Medida no encontrada'
            });
        }

        res.json({
            success: true,
            message: 'Estado actualizado correctamente'
        });
    });
};

// Eliminar medida
exports.eliminarMedida = (req, res) => {
    const { id } = req.params;

    // Primero obtener las fotos para eliminarlas
    db.get('SELECT * FROM medidas WHERE id = ?', [id], (err, row) => {
        if (err || !row) {
            return res.status(404).json({
                success: false,
                message: 'Medida no encontrada'
            });
        }

        // Eliminar archivos de fotos
        const uploadsDir = path.join(__dirname, '../uploads');
        ['foto_derecha', 'foto_izquierda', 'foto_lateral', 'foto_superior'].forEach(campo => {
            if (row[campo]) {
                const filePath = path.join(uploadsDir, row[campo]);
                if (fs.existsSync(filePath)) {
                    fs.unlinkSync(filePath);
                }
            }
        });

        // Eliminar registro de la base de datos
        db.run('DELETE FROM medidas WHERE id = ?', [id], function (err) {
            if (err) {
                console.error('Error al eliminar medida:', err);
                return res.status(500).json({
                    success: false,
                    message: 'Error al eliminar la medida'
                });
            }

            res.json({
                success: true,
                message: 'Medida eliminada correctamente'
            });
        });
    });
};
