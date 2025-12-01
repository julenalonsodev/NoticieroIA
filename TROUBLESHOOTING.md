# Troubleshooting - Problema de despliegue en EasyPanel

## Problema Actual
La aplicación se despliega pero no muestra las páginas HTML, solo devuelve JSON o errores.

## Diagnóstico

### Paso 1: Verificar endpoint de debug
Accede a: `https://digital-digital-noticieroia.owolqd.easypanel.host/debug/files`

Esto mostrará:
- El directorio de trabajo (`__dirname`)
- Qué archivos existen en cada directorio
- Si los archivos HTML, CSS, JS están presentes

### Paso 2: Verificar logs en EasyPanel
En el panel de EasyPanel, revisa los logs de la aplicación. Deberías ver:
```
✅ Servidor corriendo en http://0.0.0.0:3000
📂 Directorio de trabajo: /app
📂 Rutas de archivos estáticos:
   CSS: /app/css
   JS: /app/js
   Imágenes: /app/img
   Vistas: /app/vistas
```

### Paso 3: Verificar configuración en EasyPanel

#### Configuración CORRECTA:

1. **Tipo de aplicación**: Docker
2. **Contexto de build**: **RAÍZ del proyecto** (donde está el Dockerfile principal)
   - ❌ NO usar `beta/node` como contexto
   - ✅ Usar la raíz (`.` o el directorio raíz del repo)
3. **Dockerfile path**: `./Dockerfile` (el de la raíz)
4. **Puerto**: 3000
5. **Variables de entorno**:
   ```
   MONGODB_URI=mongodb+srv://beta:Qwerty1234@cluster0.qleqdaa.mongodb.net/beta
   PORT=3000
   NODE_ENV=production
   ```

## Soluciones

### Solución 1: Verificar contexto de build

Si EasyPanel está configurado con contexto `beta/node`:
1. Ve a la configuración de la aplicación en EasyPanel
2. Cambia el **Build Context** o **Root Directory** a la raíz del proyecto (`.`)
3. Asegúrate de que el **Dockerfile Path** sea `./Dockerfile`

### Solución 2: Verificar que los archivos se copien en el build

En los logs de build en EasyPanel, busca mensajes como:
```
WARNING: vistas directory not found
WARNING: css directory not found
```

Si ves estos warnings, significa que los archivos no se están copiando correctamente.

### Solución 3: Verificar estructura de archivos en el contenedor

Después de hacer redeploy, accede a `/debug/files` para ver qué archivos existen realmente en el contenedor.

## Verificaciones Adicionales

### Probar endpoints directamente:

1. **Health check**: `https://digital-digital-noticieroia.owolqd.easypanel.host/health`
   - Debería devolver: `{"status":"ok","timestamp":"..."}`

2. **Debug files**: `https://digital-digital-noticieroia.owolqd.easypanel.host/debug/files`
   - Debería mostrar la estructura de archivos

3. **API info**: `https://digital-digital-noticieroia.owolqd.easypanel.host/api`
   - Debería devolver información de la API

4. **Login page**: `https://digital-digital-noticieroia.owolqd.easypanel.host/`
   - Debería mostrar la página de login HTML

## Cambios Realizados

1. ✅ Actualizado `server.js` para servir archivos estáticos
2. ✅ Corregidas rutas de archivos estáticos (de `../` a `./`)
3. ✅ Agregado endpoint de debug (`/debug/files`)
4. ✅ Agregados logs de depuración
5. ✅ Actualizado Dockerfile de la raíz para copiar archivos estáticos
6. ✅ Actualizado Dockerfile de `beta/node/` para funcionar desde contexto raíz

## Próximos Pasos

1. **Verificar configuración en EasyPanel** (más importante):
   - Build Context debe ser la RAÍZ del proyecto
   - Dockerfile Path debe ser `./Dockerfile`

2. **Hacer redeploy** después de verificar la configuración

3. **Revisar logs** para ver si hay errores

4. **Probar el endpoint de debug** para ver qué archivos existen

5. Si aún no funciona, revisa los logs de build para ver si los archivos se están copiando correctamente durante el build

## Contacto

Si después de seguir estos pasos aún no funciona, proporciona:
- Screenshot de la configuración de build en EasyPanel
- Logs completos del contenedor
- Resultado de `/debug/files`

