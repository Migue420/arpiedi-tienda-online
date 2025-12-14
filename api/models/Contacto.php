<?php
require_once __DIR__ . '/../config/database.php';

class Contacto
{
    private $conn;
    private $table = 'contactos';

    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function crear($datos)
    {
        $query = "INSERT INTO " . $this->table . " 
                  (nombre, email, telefono, mensaje)
                  VALUES 
                  (:nombre, :email, :telefono, :mensaje)";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':nombre', $datos['nombre']);
        $stmt->bindParam(':email', $datos['email']);
        $stmt->bindParam(':telefono', $datos['telefono']);
        $stmt->bindParam(':mensaje', $datos['mensaje']);

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

    public function marcarLeido($id)
    {
        $query = "UPDATE " . $this->table . " SET leido = 1 WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }

    public function eliminar($id)
    {
        $query = "DELETE FROM " . $this->table . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }
}
?>