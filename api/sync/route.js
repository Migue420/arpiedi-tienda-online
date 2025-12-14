import { NextResponse } from 'next/server';
import mysql from 'mysql2/promise';

// --- CONFIGURACIÓN ---
// Esta es la dirección donde buscará los datos. 
// Asegúrate de haber subido el archivo 'puente_arpiedi.php' a tu web.
const URL_PUENTE_WEB = 'https://arpiedi.es/puente_arpiedi.php';

// Configuración de tu base de datos local (XAMPP)
const dbConfig = {
    host: process.env.DB_HOST || 'localhost',
    user: process.env.DB_USER || 'root',
    password: process.env.DB_PASSWORD || '',
    database: 'arpiedi_db',
};

export async function GET() {
    let connection;
    try {
        // 1. CONECTAR A LA WEB Y PEDIR DATOS
        console.log("Conectando a arpiedi.es...");
        const respuestaWeb = await fetch(URL_PUENTE_WEB, { cache: 'no-store' });

        if (!respuestaWeb.ok) {
            throw new Error(`Error al conectar con la web (Código: ${respuestaWeb.status}). ¿Has subido el archivo puente?`);
        }

        const datosWeb = await respuestaWeb.json();

        if (datosWeb.error) {
            throw new Error(`Error remoto: ${datosWeb.error}`);
        }

        // 2. CONECTAR A TU BASE DE DATOS LOCAL
        connection = await mysql.createConnection(dbConfig);

        let nuevosPedidos = 0;

        // 3. PROCESAR MEDIDAS (Convertirlas en Pedidos de Fabricación)
        if (datosWeb.medidas_web && Array.isArray(datosWeb.medidas_web)) {
            for (const ficha of datosWeb.medidas_web) {

                // Generamos un ID único usando el ID original de la web
                // Así evitamos duplicados (si le das 2 veces, no crea 2 pedidos iguales)
                const idLocal = `WEB-${ficha.id}`;

                // Comprobamos si ya tenemos este pedido
                const [existente] = await connection.execute(
                    'SELECT id FROM ordenes_fabricacion WHERE id = ?',
                    [idLocal]
                );

                if (existente.length === 0) {
                    // Si es nuevo, formateamos las medidas para que se lean bien
                    const medidasTexto = `IZQ: ${ficha.pie_izquierdo_largo}x${ficha.pie_izquierdo_ancho} | DER: ${ficha.pie_derecho_largo}x${ficha.pie_derecho_ancho} | NOTAS: ${ficha.notas || ''}`;

                    // LO INSERTAMOS COMO UN NUEVO PEDIDO DE FABRICACIÓN
                    await connection.execute(
                        `INSERT INTO ordenes_fabricacion 
            (id, patientName, status, measurements, paymentStatus, receiptStatus, dateCreated) 
            VALUES (?, ?, 'Medición', ?, 'Pendiente', 'No Recibido', ?)`,
                        [idLocal, ficha.cliente_nombre, medidasTexto, ficha.fecha_creacion]
                    );

                    nuevosPedidos++;
                }
            }
        }

        return NextResponse.json({
            exito: true,
            mensaje: `Sincronización completada.`,
            nuevos_pedidos: nuevosPedidos
        });

    } catch (error) {
        console.error("Error de sincronización:", error);
        return NextResponse.json({
            exito: false,
            error: error.message,
            consejo: "Asegúrate de haber subido 'puente_arpiedi.php' a la web y que los datos de conexión en ese archivo sean correctos."
        }, { status: 500 });
    } finally {
        if (connection) await connection.end();
    }
}