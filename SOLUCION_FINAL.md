# Solución Final - Configuración Docker Simple

## 🎯 Configuración Simplificada

He creado un Dockerfile más simple (`Dockerfile.simple`) que debería funcionar sin problemas.

## 📋 Pasos para Configurar en EasyPanel

### Paso 1: Cambiar de Nixpacks a Docker

1. Ve a la configuración de tu aplicación
2. Cambia el **Build Method** de **Nixpacks** a **Docker**
3. Guarda los cambios

### Paso 2: Configurar el Repositorio

1. **Repositorio Git**: `https://github.com/julenalonsodev/NoticieroIA.git`
2. **Rama**: `main`
3. **Root Directory**: `.` (la raíz del proyecto)
4. **Dockerfile Path**: `./Dockerfile.simple` (o cambia el Dockerfile principal)

### Paso 3: Variables de Entorno

Configura estas variables:

```
PORT=3000
MONGODB_URI=mongodb+srv://beta:Qwerty1234@cluster0.qleqdaa.mongodb.net/beta
NODE_ENV=production
```

**IMPORTANTE**: Asegúrate de que `PORT=3000` esté configurado.

### Paso 4: Configurar el Proxy (CRÍTICO)

1. Ve a la pestaña **"Domains"**
2. Haz clic en el **ícono de lápiz** (editar) del dominio
3. Verifica o configura:
   - **Target** o **Backend**: El nombre de tu servicio (probablemente `digital_digital_noticieroia`)
   - **Port** o **Target Port**: **3000** (NO 80)
4. Guarda los cambios

### Paso 5: Hacer Deploy

1. Haz clic en el botón verde **"Deploy"**
2. Espera a que termine el build
3. Revisa los logs para verificar que el servidor esté corriendo

## ✅ Verificación

Después del deploy:

1. **Logs deben mostrar**: `✅ Servidor corriendo en http://0.0.0.0:3000`
2. **Accede a**: `https://digital-digital-noticieroia.owolqd.easypanel.host/`
   - Debe mostrar la página de login HTML
3. **Prueba**: `https://digital-digital-noticieroia.owolqd.easypanel.host/test`
   - Debe devolver JSON con información del servidor

## 🔧 Alternativa: Usar Dockerfile.simple

Si el Dockerfile principal tiene problemas, puedes:

1. Renombrar `Dockerfile.simple` a `Dockerfile`
2. O cambiar el **Dockerfile Path** en EasyPanel a `./Dockerfile.simple`

## 🆘 Si Aún No Funciona

### Verificar el Proxy

El problema más común es que el proxy no está configurado correctamente. Asegúrate de:

1. ✅ El puerto en el proxy sea **3000** (no 80)
2. ✅ El nombre del servicio sea correcto
3. ✅ El proxy esté habilitado/activo

### Verificar el Puerto del Servidor

En los logs, debe decir:
```
✅ Servidor corriendo en http://0.0.0.0:3000
🔧 PORT: 3000
```

Si dice puerto 80, verifica las variables de entorno.

### Reiniciar el Contenedor

1. Haz clic en el botón de **refresh/reiniciar** (flecha circular)
2. Espera unos segundos
3. Vuelve a probar la URL

## 📝 Resumen de Configuración Correcta

| Configuración | Valor |
|--------------|-------|
| Build Method | Docker |
| Root Directory | `.` (raíz) |
| Dockerfile Path | `./Dockerfile` o `./Dockerfile.simple` |
| PORT (env var) | `3000` |
| Proxy Port (Domains) | `3000` |
| Proxy Target | Nombre del servicio (ej: `digital_digital_noticieroia`) |

