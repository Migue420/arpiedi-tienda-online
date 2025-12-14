<?php
require_once __DIR__ . '/../config/database.php';

class Medida
{
    private $conn;
    private $table = 'medidas';

    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function crear($datos, $fotos = [])
    {
        $query = "INSERT INTO " . $this->table . " 
                  (cliente_nombre, cliente_email, cliente_telefono,
                   pie_derecho_largo, pie_derecho_ancho,
                   pie_izquierdo_largo, pie_izquierdo_ancho,
                   foto_derecha, foto_izquierda, foto_lateral, foto_superior,
                   notas)
                  VALUES 
                  (:cliente_nombre, :cliente_email, :cliente_telefono,
                   :pie_derecho_largo, :pie_derecho_ancho,
                   :pie_izquierdo_largo, :pie_izquierdo_ancho,
                   :foto_derecha, :foto_izquierda, :foto_lateral, :foto_superior,
                   :notas)";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':cliente_nombre', $datos['cliente_nombre']);
        $stmt->bindParam(':cliente_email', $datos['cliente_email']);
        $stmt->bindParam(':cliente_telefono', $datos['cliente_telefono']);
        $stmt->bindParam(':pie_derecho_largo', $datos['pie_derecho_largo']);
        $stmt->bindParam(':pie_derecho_ancho', $datos['pie_derecho_ancho']);
        $stmt->bindParam(':pie_izquierdo_largo', $datos['pie_izquierdo_largo']);
        $stmt->bindParam(':pie_izquierdo_ancho', $datos['pie_izquierdo_ancho']);
        $stmt->bindParam(':foto_derecha', $fotos['foto_derecha']);
        $stmt->bindParam(':foto_izquierda', $fotos['foto_izquierda']);
        $stmt->bindParam(':foto_lateral', $fotos['foto_lateral']);
        $stmt->bindParam(':foto_superior', $fotos['foto_superior']);
        $stmt->bindParam(':notas', $datos['notas']);

        if ($stmt->execute()) {
            return $this->conn->lastInsertId();
        }

        return false;
    }

    public function listar()
    {
        $query = "SELECT * FROM " . $this->table . " ORDER BY fecha_creacion DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function obtenerPorId($id)
    {
        $query = "SELECT * FROM " . $this->table . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch();
    }

    public function actualizarEstado($id, $estado)
    {
        $query = "UPDATE " . $this->table . " SET estado = :estado WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':estado', $estado);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }

    public function eliminar($id)
    {
        // Primero obtener las fotos para eliminarlas
        $medida = $this->obtenerPorId($id);

        if ($medida) {
            $campos_fotos = ['foto_derecha', 'foto_izquierda', 'foto_lateral', 'foto_superior'];
            foreach ($campos_fotos as $campo) {
                if (!empty($medida[$campo])) {
                    $ruta = UPLOAD_DIR . $medida[$campo];
                    if (file_exists($ruta)) {
                        unlink($ruta);
                    }
                }
            }
        }

        $query = "DELETE FROM " . $this->table . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }
}
?>