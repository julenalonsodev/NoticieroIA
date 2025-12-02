# Agents.md - NoticieroIA

Documentación completa del proyecto para reiniciar la programación más adelante.

## 📋 Descripción del Proyecto

NoticieroIA es una aplicación web para gestionar géneros de contenido, noticias y artículos. El proyecto consta de:
- **Backend**: Servidor Node.js con Express que sirve tanto API como archivos estáticos
- **Frontend**: Páginas HTML estáticas con CSS y JavaScript
- **Base de Datos**: MongoDB Atlas (colección: `planificacioncontenido`)

## 🗂️ Estructura del Proyecto

```
NoticieroIA/
├── Dockerfile                    # Dockerfile principal en la raíz
├── docker-compose.yml            # Docker Compose para desarrollo local
├── beta/             # Aplicación PHP (legacy, no usada en Docker)
├── beta/
│   ├── node/                     # Backend Node.js
│   │   ├── server.js             # Servidor Express principal
│   │   ├── db.js                 # Conexión a MongoDB
│   │   ├── package.json          # Dependencias Node.js
│   │   └── Dockerfile            # Dockerfile específico (no usado)
│   ├── vistas/                   # Páginas HTML
│   │   ├── login.html            # Página de inicio de sesión
│   │   ├── home.html             # Página principal (géneros)
│   │   └── articulos.html        # Página de artículos
│   ├── css/                      # Estilos CSS
│   ├── js/                       # Scripts JavaScript
│   └── img/                      # Imágenes y assets
└── Agents.md                     # Este archivo
```

## 🔧 Configuración del Servidor

### Archivo: `beta/node/server.js`

El servidor Express está configurado para:
1. **Servir archivos estáticos** (HTML, CSS, JS, imágenes)
2. **Proporcionar API REST** para operaciones con MongoDB
3. **Escuchar en puerto 3000** (configurable con variable de entorno)

### Rutas Disponibles

#### Páginas Web (HTML)
- `GET /` → Login page
- `GET /login` → Login page
- `GET /home` → Home page (gestión de géneros)
- `GET /articulos` → Artículos page

#### Archivos Estáticos
- `/css/*` → Archivos CSS
- `/js/*` → Archivos JavaScript
- `/img/*` → Imágenes
- `/vistas/*` → Páginas HTML (acceso directo)

#### API Endpoints
- `GET /api` → Información de la API
- `GET /health` → Health check para Docker
- `POST /api/generos` → Insertar nuevo género de contenido

### Ejemplo de Payload para `/api/generos`:
```json
{
  "tema": "Tendencias de IA",
  "descripcion": "La IA avanza rápidamente",
  "frecuencia": "Diario",
  "cantidad": 5,
  "idioma": "es",
  "fuentes": ["BBC News", "Reuters"],
  "fecha_ingreso": "2024-01-15T10:00:00.000Z"
}
```

## 🗄️ Base de Datos

### MongoDB Atlas

- **Base de datos**: `beta`
- **Colección principal**: `planificacioncontenido`
- **URI de conexión**: Variable de entorno `MONGODB_URI` o `MONGO_URI`

### Archivo: `beta/node/db.js`

La conexión se establece usando `MongoClient` y se reutiliza la conexión existente para mejorar el rendimiento.

**Nota importante**: El código busca ambas variables `MONGODB_URI` y `MONGO_URI` para compatibilidad.

## 🐳 Configuración Docker

### Dockerfile (raíz del proyecto)

El Dockerfile usa multi-stage build:
1. **Stage 1 (builder)**: Instala dependencias de Node.js
2. **Stage 2 (production)**: Copia archivos y configura el contenedor

#### Archivos copiados:
- `beta/node/*.js` → `/app/`
- `beta/node/.env*` → `/app/`
- `beta/vistas/` → `/app/vistas/`
- `beta/css/` → `/app/css/`
- `beta/js/` → `/app/js/`
- `beta/img/` → `/app/img/`

#### Configuración:
- Usuario no-root: `nodejs` (uid: 1001)
- Puerto expuesto: `3000`
- Health check: `GET /health` cada 30 segundos
- Comando: `node server.js`

### Dockerfile.simple (para EasyPanel)

Dockerfile simplificado usado en EasyPanel:
- Copia `beta/node/package*.json` e instala dependencias
- Copia `beta/node/*.js` (código de la aplicación)
- Copia archivos estáticos desde `beta/vistas/`, `beta/css/`, `beta/js/`, `beta/img/`

**IMPORTANTE**: Los archivos estáticos deben estar en `beta/` antes del build:
- `beta/vistas/` - Archivos HTML (login.html, home.html, articulos.html)
- `beta/css/` - Archivos CSS (copiados desde `styles/`)
- `beta/js/` - Archivos JavaScript (copiados desde `code/`)
- `beta/img/` - Imágenes (copiadas desde `img/`)

### Docker Compose (desarrollo local)

Incluye dos servicios:
- **app**: Aplicación Node.js
- **mongo**: MongoDB local (opcional, normalmente se usa MongoDB Atlas)

## 🚀 Despliegue en EasyPanel

### URL de Producción
```
https://digital-digital-noticieroia.owolqd.easypanel.host
```

### Configuración en EasyPanel

1. **Tipo de aplicación**: Docker
2. **Directorio de build**: Raíz del proyecto (contiene el Dockerfile)
3. **Puerto**: 3000
4. **Variables de entorno requeridas**:
   ```
   MONGODB_URI=mongodb+srv://beta:Qwerty1234@cluster0.qleqdaa.mongodb.net/beta
   PORT=3000
   NODE_ENV=production
   ```

### Problema Resuelto

**Problema anterior**: La aplicación se desplegaba correctamente pero no mostraba ninguna página web, solo devolvía JSON.

**Solución aplicada**:
1. ✅ Configurado Express para servir archivos estáticos (HTML, CSS, JS, imágenes)
2. ✅ Agregadas rutas para las páginas principales (`/`, `/login`, `/home`, `/articulos`)
3. ✅ Actualizado Dockerfile para copiar todos los archivos estáticos
4. ✅ Corregidas las rutas en los archivos HTML para usar rutas absolutas
5. ✅ Actualizada la URL del formulario en `home.html` para usar la API local (`/api/generos`)

## 🔄 Cambios Recientes

### 2024-01-XX - Fix rutas de archivos estáticos (segundo intento)

**Problema**: Después del primer fix, la aplicación aún no mostraba las páginas HTML tras el despliegue.

**Causa**: Las rutas en `server.js` usaban `../` (directorio padre) cuando debían usar `./` (directorio actual), ya que todos los archivos están copiados en `/app/` dentro del contenedor.

**Solución aplicada**:
1. ✅ Cambiadas todas las rutas de `../` a `./` en `server.js`
2. ✅ Agregado manejo de errores con callbacks en `res.sendFile()`
3. ✅ Agregados logs de depuración para identificar problemas
4. ✅ Agregado middleware 404 para rutas no encontradas
5. ✅ Agregados logs del directorio de trabajo y rutas de archivos estáticos al iniciar

### 2024-01-XX - Fix despliegue EasyPanel (primer intento)

1. **server.js**:
   - Agregado middleware para servir archivos estáticos
   - Configuradas rutas para servir páginas HTML
   - Mantenida compatibilidad con endpoints API existentes

2. **Dockerfile**:
   - Agregada copia de archivos estáticos (vistas, css, js, img)
   - Mantenida estructura de directorios dentro del contenedor

3. **HTML Files**:
   - `login.html`: Actualizadas rutas CSS e imágenes a rutas absolutas
   - `home.html`: Actualizadas rutas de enlaces y URL de API
   - Todas las referencias ahora usan rutas absolutas (`/css/`, `/img/`, etc.)

## 📦 Dependencias Node.js

Ver `beta/node/package.json`:
- express: ^5.1.0
- mongodb: ^7.0.0
- cors: ^2.8.5
- body-parser: ^2.2.0
- dotenv: ^17.2.3

## 🔍 Troubleshooting

### Error: "/beta/img": not found en Docker build

**Error**: 
```
ERROR: failed to build: failed to solve: failed to compute cache key: 
failed to calculate checksum of ref ... "/beta/img": not found
```

**Causa**: El `Dockerfile.simple` intenta copiar directorios desde `beta/vistas/`, `beta/css/`, `beta/js/`, `beta/img/` que no existen.

**Solución aplicada**:
1. ✅ Crear los directorios necesarios en `beta/`:
   - `beta/vistas/` - Archivos HTML
   - `beta/css/` - Archivos CSS (copiados desde `styles/`)
   - `beta/js/` - Archivos JavaScript (copiados desde `code/`)
   - `beta/img/` - Imágenes (copiadas desde `img/`)
2. ✅ Crear archivos HTML básicos en `beta/vistas/`:
   - `login.html`
   - `home.html`
   - `articulos.html`
3. ✅ Copiar archivos estáticos desde la raíz a `beta/`:
   ```bash
   mkdir -p beta/vistas beta/css beta/js beta/img
   cp -r styles/* beta/css/
   cp -r code/* beta/js/
   cp -r img/* beta/img/
   ```

**Prevención**: Asegurarse de que todos los directorios en `beta/` existan antes de hacer el build en EasyPanel.

### La aplicación no muestra páginas HTML
- ✅ **RESUELTO**: Ver cambios recientes arriba
- Verificar que los archivos estáticos estén siendo copiados en el Dockerfile
- Verificar logs del contenedor para errores de rutas

### Error de conexión a MongoDB
- Verificar variable de entorno `MONGODB_URI` o `MONGO_URI`
- Verificar que MongoDB Atlas permita conexiones desde la IP del servidor
- Verificar nombre de la base de datos: debe ser `beta`

### Health check falla
- Verificar que el endpoint `/health` responda correctamente
- Revisar logs del contenedor para ver si el servidor está iniciando

### Archivos estáticos no se cargan (404)
- Verificar que las rutas en HTML usen rutas absolutas (empezando con `/`)
- Verificar que Express esté configurado para servir archivos estáticos
- Verificar que los archivos estén en las rutas correctas dentro del contenedor

## 📝 Notas de Desarrollo

### Para desarrollo local:
```bash
cd beta/node
npm install
node server.js
```

### Para testing con Docker local:
```bash
docker build -t noticieroia .
docker run -p 3000:3000 -e MONGODB_URI="..." noticieroia
```

### Para ver logs en EasyPanel:
- Ir al panel de EasyPanel
- Seleccionar la aplicación
- Ver logs en tiempo real

## 🔐 Seguridad

- Las credenciales de MongoDB deberían estar en variables de entorno
- Considerar cambiar las credenciales de MongoDB en producción
- El servidor escucha en `0.0.0.0` para ser accesible desde fuera del contenedor (necesario para Docker)

## 📚 Referencias

- Documentación de EasyPanel: https://easypanel.io/docs
- MongoDB Atlas: https://www.mongodb.com/cloud/atlas
- Express.js: https://expressjs.com/

---

**Última actualización**: 2025-12-02 - Fix error Docker build: Creados directorios faltantes en `beta/` (vistas, css, js, img) y corregido `Dockerfile.simple` para copiar archivos estáticos correctamente.

