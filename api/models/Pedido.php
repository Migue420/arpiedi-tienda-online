<?php
require_once __DIR__ . '/../config/database.php';

class Pedido
{
    private $conn;
    private $table = 'pedidos';
    private $table_items = 'pedidos_items';

    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function crear($datos, $items)
    {
        try {
            $this->conn->beginTransaction();

            // Insertar pedido
            $query = "INSERT INTO " . $this->table . " 
                      (cliente_nombre, cliente_email, cliente_telefono, cliente_direccion, total)
                      VALUES 
                      (:cliente_nombre, :cliente_email, :cliente_telefono, :cliente_direccion, :total)";

            $stmt = $this->conn->prepare($query);

            $stmt->bindParam(':cliente_nombre', $datos['cliente_nombre']);
            $stmt->bindParam(':cliente_email', $datos['cliente_email']);
            $stmt->bindParam(':cliente_telefono', $datos['cliente_telefono']);
            $stmt->bindParam(':cliente_direccion', $datos['cliente_direccion']);
            $stmt->bindParam(':total', $datos['total']);

            $stmt->execute();
            $pedido_id = $this->conn->lastInsertId();

            // Insertar items del pedido
            $query_items = "INSERT INTO " . $this->table_items . " 
                            (pedido_id, producto_nombre, cantidad, precio_unitario)
                            VALUES 
                            (:pedido_id, :producto_nombre, :cantidad, :precio_unitario)";

            $stmt_items = $this->conn->prepare($query_items);

            foreach ($items as $item) {
                $stmt_items->bindParam(':pedido_id', $pedido_id);
                $stmt_items->bindParam(':producto_nombre', $item['producto_nombre']);
                $stmt_items->bindParam(':cantidad', $item['cantidad']);
                $stmt_items->bindParam(':precio_unitario', $item['precio_unitario']);
                $stmt_items->execute();
            }

            $this->conn->commit();
            return $pedido_id;

        } catch (Exception $e) {
            $this->conn->rollBack();
            return false;
        }
    }

    public function listar()
    {
        $query = "SELECT p.*, 
                  (SELECT COUNT(*) FROM " . $this->table_items . " WHERE pedido_id = p.id) as num_items
                  FROM " . $this->table . " p 
                  ORDER BY p.fecha_creacion DESC";
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
        $pedido = $stmt->fetch();

        if ($pedido) {
            // Obtener items del pedido
            $query_items = "SELECT * FROM " . $this->table_items . " WHERE pedido_id = :pedido_id";
            $stmt_items = $this->conn->prepare($query_items);
            $stmt_items->bindParam(':pedido_id', $id);
            $stmt_items->execute();
            $pedido['items'] = $stmt_items->fetchAll();
        }

        return $pedido;
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
        // Los items se eliminan automáticamente por CASCADE
        $query = "DELETE FROM " . $this->table . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }
}
?>