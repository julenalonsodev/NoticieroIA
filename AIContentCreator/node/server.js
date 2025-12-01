require('dotenv').config();
const express = require('express');
const bodyParser = require('body-parser');
const cors = require('cors');
const path = require('path');
const { conectar } = require('./db');

const app = express();
const PORT = process.env.PORT || 3000;

// Middleware
app.use(cors());           // Permite solicitudes desde cualquier origen
app.use(bodyParser.json()); // Permite leer JSON del body

// Servir archivos estáticos (desde /app ya que server.js está en /app)
app.use('/css', express.static(path.join(__dirname, './css')));
app.use('/js', express.static(path.join(__dirname, './js')));
app.use('/img', express.static(path.join(__dirname, './img')));
app.use('/vistas', express.static(path.join(__dirname, './vistas')));

// Rutas para las vistas HTML
app.get('/', (req, res) => {
  const filePath = path.join(__dirname, './vistas/login.html');
  console.log('Serving login.html from:', filePath);
  res.sendFile(filePath, (err) => {
    if (err) {
      console.error('Error serving login.html:', err);
      res.status(500).send('Error loading page');
    }
  });
});

app.get('/login', (req, res) => {
  const filePath = path.join(__dirname, './vistas/login.html');
  res.sendFile(filePath, (err) => {
    if (err) {
      console.error('Error serving login.html:', err);
      res.status(500).send('Error loading page');
    }
  });
});

app.get('/home', (req, res) => {
  const filePath = path.join(__dirname, './vistas/home.html');
  res.sendFile(filePath, (err) => {
    if (err) {
      console.error('Error serving home.html:', err);
      res.status(500).send('Error loading page');
    }
  });
});

app.get('/articulos', (req, res) => {
  const filePath = path.join(__dirname, './vistas/articulos.html');
  res.sendFile(filePath, (err) => {
    if (err) {
      console.error('Error serving articulos.html:', err);
      res.status(500).send('Error loading page');
    }
  });
});

// API Info endpoint
app.get('/api', (req, res) => {
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

// Debug endpoint - verificar estructura de archivos
app.get('/debug/files', (req, res) => {
  const fs = require('fs');
  const debugInfo = {
    __dirname: __dirname,
    cwd: process.cwd(),
    files: {}
  };
  
  try {
    debugInfo.files.app = fs.readdirSync(__dirname);
    debugInfo.files.vistas = fs.existsSync(path.join(__dirname, './vistas')) 
      ? fs.readdirSync(path.join(__dirname, './vistas'))
      : 'NO EXISTE';
    debugInfo.files.css = fs.existsSync(path.join(__dirname, './css'))
      ? fs.readdirSync(path.join(__dirname, './css'))
      : 'NO EXISTE';
    debugInfo.files.js = fs.existsSync(path.join(__dirname, './js'))
      ? fs.readdirSync(path.join(__dirname, './js'))
      : 'NO EXISTE';
    debugInfo.files.img = fs.existsSync(path.join(__dirname, './img'))
      ? fs.readdirSync(path.join(__dirname, './img'))
      : 'NO EXISTE';
    
    // Verificar archivos específicos
    const loginHtml = path.join(__dirname, './vistas/login.html');
    debugInfo.files.loginHtml = fs.existsSync(loginHtml) ? 'EXISTE' : 'NO EXISTE';
    
  } catch (err) {
    debugInfo.error = err.message;
  }
  
  res.json(debugInfo);
});

app.post('/api/generos', async (req, res) => {
  try {
    console.log('Datos recibidos del formulario:', req.body);
    const db = await conectar();
    const coleccion = db.collection('planificacioncontenido');

    const genero = req.body;

    const resultado = await coleccion.insertOne({
      ...genero,
      fecha_ingreso: new Date()
    });

    console.log('Documento insertado con ID:', resultado.insertedId);
    res.json({ status: 'ok', id: resultado.insertedId });
  } catch (err) {
    console.error('Error en /api/generos:', err);
    res.status(500).json({ status: 'error', error: err.message });
  }
});

// Endpoint de prueba simple (no depende de MongoDB ni archivos)
app.get('/test', (req, res) => {
  res.json({ 
    status: 'ok', 
    message: 'Servidor Node.js funcionando correctamente',
    timestamp: new Date().toISOString(),
    port: PORT,
    __dirname: __dirname,
    hostname: req.hostname,
    ip: req.ip,
    url: req.url,
    originalUrl: req.originalUrl,
    method: req.method
  });
});

// Endpoint de diagnóstico completo
app.get('/diagnostic', (req, res) => {
  const fs = require('fs');
  const diagnostic = {
    server: {
      status: 'running',
      port: PORT,
      __dirname: __dirname,
      cwd: process.cwd()
    },
    request: {
      hostname: req.hostname,
      ip: req.ip,
      url: req.url,
      originalUrl: req.originalUrl,
      method: req.method,
      headers: {
        host: req.headers.host,
        'x-forwarded-for': req.headers['x-forwarded-for'],
        'x-forwarded-proto': req.headers['x-forwarded-proto']
      }
    },
    files: {
      serverJs: fs.existsSync(path.join(__dirname, './server.js')) ? 'EXISTS' : 'NOT FOUND',
      vistas: fs.existsSync(path.join(__dirname, './vistas')) ? 'EXISTS' : 'NOT FOUND',
      loginHtml: fs.existsSync(path.join(__dirname, './vistas/login.html')) ? 'EXISTS' : 'NOT FOUND'
    },
    environment: {
      PORT: process.env.PORT,
      NODE_ENV: process.env.NODE_ENV,
      MONGODB_URI: process.env.MONGODB_URI ? 'SET' : 'NOT SET'
    }
  };
  
  res.json(diagnostic);
});

// Middleware para manejar 404 - debe ir ANTES del listen
// Pero como está después de todas las rutas, está bien aquí
app.use((req, res) => {
  console.log(`❌ Ruta no encontrada: ${req.method} ${req.url}`);
  res.status(404).json({ 
    error: 'Ruta no encontrada', 
    path: req.url,
    availableRoutes: ['/', '/login', '/home', '/articulos', '/api', '/health', '/debug/files', '/test']
  });
});

// Manejo de errores no capturados
process.on('uncaughtException', (err) => {
  console.error('❌ Uncaught Exception:', err);
  // No salir del proceso para mantener el servidor corriendo
});

process.on('unhandledRejection', (reason, promise) => {
  console.error('❌ Unhandled Rejection at:', promise, 'reason:', reason);
  // No salir del proceso para mantener el servidor corriendo
});

// Start server on 0.0.0.0 to be accessible from outside the container
try {
  app.listen(PORT, '0.0.0.0', () => {
    console.log(`✅ Servidor corriendo en http://0.0.0.0:${PORT}`);
    console.log(`📂 Directorio de trabajo: ${__dirname}`);
    console.log(`📂 Rutas de archivos estáticos:`);
    console.log(`   CSS: ${path.join(__dirname, './css')}`);
    console.log(`   JS: ${path.join(__dirname, './js')}`);
    console.log(`   Imágenes: ${path.join(__dirname, './img')}`);
    console.log(`   Vistas: ${path.join(__dirname, './vistas')}`);
    console.log(`📍 Páginas disponibles:`);
    console.log(`   GET  / - Login`);
    console.log(`   GET  /home - Home`);
    console.log(`   GET  /articulos - Artículos`);
    console.log(`📍 API Endpoints:`);
    console.log(`   GET  /api - API info`);
    console.log(`   GET  /health - Health check`);
    console.log(`   GET  /debug/files - Debug archivos`);
    console.log(`   POST /api/generos - Insertar contenido`);
    console.log(`🔧 MongoDB URI configurado: ${process.env.MONGODB_URI || process.env.MONGO_URI ? 'Sí' : 'No'}`);
    console.log(`🔧 PORT: ${PORT}`);
  });
} catch (err) {
  console.error('❌ Error al iniciar el servidor:', err);
  process.exit(1);
}
