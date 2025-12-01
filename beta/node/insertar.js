const { conectar } = require('./db');

async function insertarArticulo() {
  const db = await conectar();
  const coleccion = db.collection('articulos');

  const articulo = {
    title: "Neo 1X: El Futuro de los Robots Domésticos Está Más Cerca de lo que Crees 🤖🏠",
    description: `La presentación del robot Neo X ha generado gran expectación...
    ...`, // tu texto completo
    image: "https://i.ibb.co/33s9m4d/ff72f475deb2.jpg",
    estado_noticia: "Nuevo",
    estado_imagen: "Nuevo",
    publicacion: { estado: "Pendiente", fecha_publicada: null },
    fuentes: ["https://ejemplo.com"],
    fecha_ingreso: new Date()
  };

  const resultado = await coleccion.insertOne(articulo);
  console.log('Documento insertado con ID:', resultado.insertedId);
}

insertarArticulo();
