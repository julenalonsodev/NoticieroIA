const { conectar } = require('./db');

async function insertarGenero(data) {
    try {
        const db = await conectar();
        const coleccion = db.collection('planificacioncontenido');

        // Agregar fecha de creación automática
        data.fecha_ingreso = new Date();

        const resultado = await coleccion.insertOne(data);
        console.log('Género insertado con ID:', resultado.insertedId);
    } catch (err) {
        console.error('Error al insertar el género:', err);
    }
}

// Para probar desde Node
// insertarGenero({ tema: "Prueba", descripcion: "Desc prueba", frecuencia: "Diario", cantidad: 5, idioma: "es", fuentes: ["BBC"] });

module.exports = { insertarGenero };
