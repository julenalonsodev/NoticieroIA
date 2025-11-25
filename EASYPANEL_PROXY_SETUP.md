# Configuración del Proxy en EasyPanel

## 🔍 Problema Identificado

La sección de **Ports** en EasyPanel dice:

> "If you want to expose HTTP/HTTPS you should use the 'Proxy' from the 'Domains' tab."

Esto significa que **NO debes configurar puertos** para aplicaciones web. En su lugar, debes usar el **Proxy** desde la pestaña **"Domains"**.

## ✅ Solución: Configurar el Proxy

### Paso 1: Ve a la pestaña "Domains"

En EasyPanel, en tu aplicación `digital / digital_noticieroia`:
1. Busca la pestaña **"Domains"** o **"Domain"** en la parte superior
2. Haz clic en ella

### Paso 2: Configurar el Proxy

En la sección de Domains, deberías ver:
- Una opción para agregar un dominio
- Una configuración de **Proxy** o **Port**

Configura:
- **Target Port**: `3000` (el puerto interno donde corre tu aplicación Node.js)
- **Domain**: Debería estar configurado como `digital-digital-noticieroia.owolqd.easypanel.host`

### Paso 3: Verificar Variables de Entorno

Asegúrate de que en la pestaña de **Variables de Entorno** (Environment Variables) tengas:

```
PORT=3000
MONGODB_URI=mongodb+srv://AIContentCreator:Qwerty1234@cluster0.qleqdaa.mongodb.net/AIContentCreator
NODE_ENV=production
```

**IMPORTANTE**: `PORT=3000`, NO `PORT=80`

### Paso 4: Hacer Deploy

1. Guarda todos los cambios
2. Haz clic en el botón **"Deploy"** (verde)
3. Espera a que termine el despliegue

## 📋 Resumen de Configuración

### Pestaña "Domains":
- ✅ Proxy habilitado
- ✅ Target Port: `3000`

### Pestaña "Environment Variables":
- ✅ `PORT=3000`
- ✅ `MONGODB_URI=...`
- ✅ `NODE_ENV=production`

### Sección "Ports":
- ❌ **NO configurar nada aquí** (solo para apps no-web)

## ✅ Verificación

Después de configurar el proxy correctamente:

1. Los logs deberían mostrar: `✅ Servidor corriendo en http://0.0.0.0:3000`
2. Deberías poder acceder a: `https://digital-digital-noticieroia.owolqd.easypanel.host/`
3. El endpoint `/test` debería responder con JSON

## 🆘 Si No Funciona

1. Verifica que el proxy esté habilitado en la pestaña Domains
2. Verifica que el Target Port sea `3000`
3. Verifica que `PORT=3000` en las variables de entorno
4. Haz un nuevo Deploy después de cada cambio
5. Revisa los logs para ver si hay errores

