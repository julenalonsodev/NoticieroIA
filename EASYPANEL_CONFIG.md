# Configuración de EasyPanel - IMPORTANTE

## 🔴 Problema Detectado

Al acceder a `https://digital-digital-noticieroia.owolqd.easypanel.host`, se muestra una página "Not Found" que **NO viene del servidor Node.js**. Esto indica que:

1. ❌ El contenedor Docker NO se está ejecutando, O
2. ❌ El contenedor se está crasheando al iniciar, O  
3. ❌ EasyPanel NO está configurado para redirigir el tráfico al contenedor

## ✅ Configuración CORRECTA en EasyPanel

### Paso 1: Verificar Tipo de Aplicación
- Tipo: **Docker**

### Paso 2: Configuración del Repositorio
- **Repositorio Git**: `https://github.com/julenalonsodev/NoticieroIA.git`
- **Rama**: `main`
- **Contexto de Build** o **Root Directory**: 
  - ✅ **DEBE SER LA RAÍZ** (`.`) o vacío
  - ❌ **NO usar** `beta/node`
- **Dockerfile Path**: `./Dockerfile` (el de la raíz)

### Paso 3: Configuración del Puerto
- **Puerto interno del contenedor**: `3000`
- **Puerto expuesto**: `3000`

### Paso 4: Variables de Entorno
Asegúrate de que estas variables estén configuradas:

```
MONGODB_URI=mongodb+srv://AIContentCreator:Qwerty1234@cluster0.qleqdaa.mongodb.net/AIContentCreator
PORT=3000
NODE_ENV=production
```

**Nota**: El servidor funcionará incluso sin MongoDB, pero la funcionalidad de guardar datos no estará disponible.

## 🔍 Verificar Estado del Contenedor

En EasyPanel, ve a la sección de **Logs** o **Container Status** y verifica:

1. ✅ El contenedor está en estado "Running"
2. ✅ Los logs muestran: "✅ Servidor corriendo en http://0.0.0.0:3000"
3. ❌ Si ves errores, cópialos y revisa el troubleshooting

## 🧪 Endpoints de Prueba

Después de corregir la configuración, prueba estos endpoints:

1. **Test básico**: `https://digital-digital-noticieroia.owolqd.easypanel.host/test`
   - Debe devolver JSON con información del servidor

2. **Health check**: `https://digital-digital-noticieroia.owolqd.easypanel.host/health`
   - Debe devolver: `{"status":"ok","timestamp":"..."}`

3. **Debug files**: `https://digital-digital-noticieroia.owolqd.easypanel.host/debug/files`
   - Muestra qué archivos existen en el contenedor

4. **Página principal**: `https://digital-digital-noticieroia.owolqd.easypanel.host/`
   - Debe mostrar la página de login HTML

## ⚠️ Problema Común: Contexto de Build Incorrecto

Si el **Build Context** está configurado como `beta/node`:

1. El Dockerfile no podrá copiar los archivos desde `beta/vistas`, `beta/css`, etc.
2. El contenedor no tendrá los archivos estáticos necesarios
3. El servidor puede iniciar pero no servirá las páginas HTML

**Solución**: Cambia el Build Context a la **RAÍZ** del proyecto (`.` o vacío).

## 📝 Pasos para Corregir

1. Ve a la configuración de la aplicación en EasyPanel
2. Busca la sección "Build" o "Source"
3. Cambia el **Root Directory** o **Build Context** a la raíz (`.`)
4. Asegúrate de que el **Dockerfile Path** sea `./Dockerfile`
5. Guarda los cambios
6. Haz clic en **Redeploy** o **Restart**
7. Espera a que el despliegue termine
8. Revisa los logs para ver si hay errores
9. Prueba los endpoints mencionados arriba

## 🆘 Si Aún No Funciona

1. **Revisa los logs de build**: ¿Se completó el build correctamente?
2. **Revisa los logs del contenedor**: ¿Hay errores al iniciar?
3. **Verifica el estado del contenedor**: ¿Está en estado "Running"?
4. **Prueba el endpoint /test**: Si no responde, el servidor no está corriendo

## 📞 Información para Soporte

Si necesitas ayuda, proporciona:
- Screenshot de la configuración de Build en EasyPanel
- Logs completos del contenedor (de los últimos 100 líneas)
- Estado actual del contenedor (Running/Stopped/Crashed)
- Resultado de acceder a `/test` endpoint

