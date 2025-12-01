# Guía de Despliegue con Nixpacks en EasyPanel

## 🎯 Ventajas de Nixpacks

- ✅ Configuración más simple que Docker
- ✅ Build automático
- ✅ Menos problemas de configuración de proxy
- ✅ Detección automática de Node.js

## 📋 Configuración en EasyPanel

### Paso 1: Cambiar el Método de Build

1. Ve a tu aplicación en EasyPanel
2. Ve a la configuración (ícono de llave/ajustes)
3. Busca la sección **"Build"** o **"Build Method"**
4. Cambia de **Docker** a **Nixpacks**

### Paso 2: Configurar el Repositorio

1. **Repositorio Git**: `https://github.com/julenalonsodev/NoticieroIA.git`
2. **Rama**: `main`
3. **Root Directory**: `beta/node` (importante: el directorio donde está server.js y package.json)

### Paso 3: Variables de Entorno

En la sección de **Environment Variables**, configura:

```
PORT=3000
MONGODB_URI=mongodb+srv://beta:Qwerty1234@cluster0.qleqdaa.mongodb.net/beta
NODE_ENV=production
```

### Paso 4: Configurar el Proxy (Domains)

1. Ve a la pestaña **"Domains"**
2. Edita el dominio existente
3. Asegúrate de que el **Target Port** sea **3000**
4. Guarda los cambios

### Paso 5: Deploy

1. Haz clic en el botón verde **"Deploy"**
2. Espera a que Nixpacks construya la aplicación
3. Revisa los logs para ver el progreso

## 📁 Archivos Creados

He creado dos archivos de configuración de Nixpacks:

1. **`nixpacks.toml`** (en la raíz) - Para usar la raíz como contexto
2. **`beta/node/nixpacks.toml`** (recomendado) - Para usar beta/node como contexto

## ✅ Configuración Recomendada

### Opción 1: Usar beta/node como Root Directory (RECOMENDADO)

- **Root Directory**: `beta/node`
- **Build Method**: Nixpacks
- Nixpacks detectará automáticamente Node.js
- El archivo `beta/node/nixpacks.toml` copiará los archivos estáticos

### Opción 2: Usar la raíz como Root Directory

- **Root Directory**: `.` (raíz)
- **Build Method**: Nixpacks
- Usará el archivo `nixpacks.toml` de la raíz

## 🔧 Si los Archivos Estáticos No Se Copian

Si después del deploy los archivos estáticos (HTML, CSS, JS) no están disponibles, puedes:

1. Verificar en los logs si hay errores al copiar archivos
2. El archivo `beta/node/nixpacks.toml` tiene comandos para copiar los archivos desde el directorio padre

## 📝 Estructura Esperada con Nixpacks

Con Root Directory = `beta/node`, Nixpacks esperará:
- `package.json` en `beta/node/`
- `server.js` en `beta/node/`
- Los archivos estáticos se copiarán durante el build desde `beta/vistas`, `beta/css`, etc.

## ✅ Verificación

Después del deploy con Nixpacks:

1. Los logs deberían mostrar: `✅ Servidor corriendo en http://0.0.0.0:3000`
2. Accede a: `https://digital-digital-noticieroia.owolqd.easypanel.host/`
3. Debería mostrar la página de login
4. Prueba: `https://digital-digital-noticieroia.owolqd.easypanel.host/test`

## 🆘 Troubleshooting

### Si Nixpacks no detecta Node.js:
- Verifica que `package.json` esté en el Root Directory
- Verifica que el Root Directory sea `beta/node`

### Si los archivos estáticos no están:
- Revisa los logs del build
- Verifica que los comandos de copia en `nixpacks.toml` estén funcionando
- Puedes necesitar ajustar las rutas en los comandos de copia

### Si el puerto es incorrecto:
- Verifica que `PORT=3000` esté en las variables de entorno
- Verifica que el proxy en Domains apunte al puerto 3000

## 🔄 Comparación con Docker

| Aspecto | Docker | Nixpacks |
|---------|--------|----------|
| Configuración | Más compleja | Más simple |
| Build | Manual (Dockerfile) | Automático |
| Archivos estáticos | Copiar manualmente | Scripts en nixpacks.toml |
| Proxy | Puede ser problemático | Más confiable |

