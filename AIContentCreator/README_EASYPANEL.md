# ⚠️ IMPORTANTE: Configuración de EasyPanel

## 🔴 Problema Detectado

EasyPanel está configurado con:
- **Directorio de ejecución**: `/AIContentCreator`
- **Dockerfile**: `Dockerfile.simple`

Esto causa un error porque:
1. Docker **NO puede** copiar archivos fuera del contexto de build usando `../`
2. Los archivos de la aplicación Node.js están en `../beta/node/` (fuera del contexto)
3. El Dockerfile necesita acceso a `beta/node/`, `beta/vistas/`, `beta/css/`, etc.

## ✅ Solución: Cambiar el Contexto de Build en EasyPanel

### Paso 1: Ir a la configuración de la aplicación en EasyPanel

### Paso 2: Cambiar el "Root Directory" o "Build Context"

**Cambiar de:**
```
Root Directory: /AIContentCreator
```

**A:**
```
Root Directory: . (raíz del proyecto) o vacío
```

### Paso 3: Actualizar el Dockerfile Path

**Cambiar de:**
```
Dockerfile Path: Dockerfile.simple
```

**A:**
```
Dockerfile Path: Dockerfile.simple
```
(O usar `Dockerfile` que es el principal)

### Paso 4: Guardar y Redesplegar

1. Guarda los cambios en EasyPanel
2. Haz clic en **Redeploy** o **Restart**
3. Espera a que el build termine
4. Verifica los logs

## 📋 Configuración Correcta en EasyPanel

- **Tipo de aplicación**: Docker
- **Root Directory** o **Build Context**: `.` (raíz del proyecto) o **vacío**
- **Dockerfile Path**: `Dockerfile.simple` o `Dockerfile`
- **Puerto**: `3000`
- **Variables de entorno**:
  ```
  MONGODB_URI=mongodb+srv://beta:Qwerty1234@cluster0.qleqdaa.mongodb.net/beta
  PORT=3000
  NODE_ENV=production
  ```

## 🔍 Verificación

Después de cambiar la configuración, verifica que:
1. ✅ El build se complete sin errores
2. ✅ Los logs muestren: "✅ Servidor corriendo en http://0.0.0.0:3000"
3. ✅ El endpoint `/health` responda correctamente
4. ✅ Las páginas HTML se muestren correctamente

## ❓ ¿Por qué no funciona con `/AIContentCreator`?

Docker tiene una limitación de seguridad: **no puede acceder a archivos fuera del contexto de build**. 

Si el contexto es `/AIContentCreator`:
- ✅ Puede acceder a archivos en `/AIContentCreator/`
- ❌ NO puede acceder a `../beta/node/` (fuera del contexto)

Por eso es necesario usar la **raíz del proyecto** como contexto de build.

