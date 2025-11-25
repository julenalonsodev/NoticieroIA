require('dotenv').config();
const express = require('express');
const bodyParser = require('body-parser');
const cors = require('cors');
const { conectar } = require('./db');

const app = express();
const PORT = process.env.PORT || 3000;

app.use(cors());           // Permite solicitudes desde cualquier origen (útil para localhost)
app.use(bodyParser.json()); // Permite leer JSON del body

// Root route
app.get('/', (req, res) => {
  res.json({ 
    status: 'ok', 
    message: 'NoticieroIA API',
    version: '1.0.0',
    endpoints: {
      health: '/health',
      generos: 'POST /api/generos'
    }
  });
});

// Health check endpoint
app.get('/health', (req, res) => {
  res.json({ status: 'ok', timestamp: new Date().toISOString() });
});

app.post('/api/generos', async (req, res) => {
  try {
    console.log('Datos recibidos del formulario:', req.body); // <-- Aquí se ve qué datos llegan
    const db = await conectar();
    const coleccion = db.collection('planificacioncontenido');

    const genero = req.body;

    const resultado = await coleccion.insertOne({
      ...genero,
      fecha_ingreso: new Date()
    });

    console.log('Documento insertado con ID:', resultado.insertedId); // <-- DEBUG
    res.json({ status: 'ok', id: resultado.insertedId });
  } catch (err) {
    console.error(err);
    res.status(500).json({ status: 'error', error: err.message });
  }
});

app.listen(PORT, () => {
  console.log(`Servidor corriendo en http://localhost:${PORT}`);
});
