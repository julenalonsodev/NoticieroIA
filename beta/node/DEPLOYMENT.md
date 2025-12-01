# Guía de Despliegue en EasyPanel

Esta guía te ayudará a desplegar tu aplicación Node.js en EasyPanel usando Docker.

## 📋 Requisitos Previos

- Cuenta en EasyPanel
- MongoDB Atlas configurado y accesible desde internet
- Repositorio Git con el código (GitHub, GitLab, etc.)

## 🚀 Pasos para Desplegar en EasyPanel

### 1. Preparar MongoDB Atlas

> [!IMPORTANT]
> Asegúrate de que tu cluster de MongoDB Atlas permita conexiones desde cualquier IP o desde las IPs de EasyPanel.

1. Ve a MongoDB Atlas → Network Access
2. Añade la IP `0.0.0.0/0` (permite todas las IPs) o las IPs específicas de tu servidor EasyPanel
3. Verifica que tu `MONGO_URI` sea correcta

### 2. Crear Aplicación en EasyPanel

1. **Inicia sesión** en tu panel de EasyPanel
2. **Crea un nuevo proyecto** o selecciona uno existente
3. **Añade una nueva aplicación**:
   - Tipo: **Docker**
   - Nombre: `noticieroia` (o el nombre que prefieras)

### 3. Configurar el Repositorio

1. **Conecta tu repositorio Git**:
   - Proporciona la URL de tu repositorio
   - Selecciona la rama (normalmente `main` o `master`)
   - Especifica el directorio: `beta/node`

2. **Configurar Build**:
   - Build Method: **Dockerfile**
   - Dockerfile Path: `./Dockerfile` (relativo a `beta/node`)

### 4. Configurar Variables de Entorno

En la sección de **Environment Variables**, añade:

```
MONGO_URI=mongodb+srv://beta:Qwerty1234@cluster0.qleqdaa.mongodb.net
PORT=3000
NODE_ENV=production
```

> [!WARNING]
> **Seguridad**: Considera cambiar las credenciales de MongoDB y usar variables de entorno seguras en producción.

### 5. Configurar Puerto

- **Puerto de la aplicación**: `3000`
- EasyPanel automáticamente mapeará este puerto a un dominio público

### 6. Desplegar

1. Haz clic en **Deploy** o **Create**
2. EasyPanel construirá la imagen Docker y desplegará tu aplicación
3. Espera a que el despliegue termine (puedes ver los logs en tiempo real)

### 7. Verificar el Despliegue

Una vez desplegado, EasyPanel te proporcionará una URL. Prueba tu API:

```bash
curl -X POST https://tu-app.easypanel.host/api/generos \
  -H "Content-Type: application/json" \
  -d '{"titulo": "Test", "descripcion": "Prueba desde EasyPanel"}'
```

Deberías recibir una respuesta como:
```json
{"status": "ok", "id": "..."}
```

## 🔧 Testing Local con Docker

Antes de desplegar, puedes probar localmente:

### Construir la imagen:
```bash
cd beta/node
docker build -t noticieroia .
```

### Ejecutar el contenedor:
```bash
docker run -p 3000:3000 --env-file .env noticieroia
```

### O usar Docker Compose:
```bash
docker-compose up
```

### Probar la API:
```bash
curl -X POST http://localhost:3000/api/generos \
  -H "Content-Type: application/json" \
  -d '{"titulo": "Test Local", "descripcion": "Prueba local"}'
```

## 📝 Notas Importantes

### Health Check
El Dockerfile incluye un health check que verifica que la aplicación esté respondiendo correctamente. EasyPanel puede usar esto para monitorear el estado de tu aplicación.

### Logs
Puedes ver los logs de tu aplicación en el panel de EasyPanel para debugging.

### Actualizaciones
Para actualizar tu aplicación:
1. Haz push de los cambios a tu repositorio Git
2. En EasyPanel, haz clic en **Redeploy** o configura auto-deploy

### Dominio Personalizado
EasyPanel te permite configurar un dominio personalizado en la configuración de la aplicación.

## 🐛 Troubleshooting

### La aplicación no se conecta a MongoDB
- Verifica que la `MONGO_URI` sea correcta
- Asegúrate de que MongoDB Atlas permita conexiones desde la IP de EasyPanel
- Revisa los logs en EasyPanel para ver errores específicos

### Error al construir la imagen
- Verifica que el `Dockerfile` esté en el directorio correcto
- Asegúrate de que `package.json` tenga todas las dependencias necesarias
- Revisa los logs de build en EasyPanel

### La aplicación se reinicia constantemente
- Revisa los logs para ver el error
- Verifica las variables de entorno
- Asegúrate de que el puerto 3000 esté expuesto correctamente

## 📚 Recursos Adicionales

- [Documentación de EasyPanel](https://easypanel.io/docs)
- [MongoDB Atlas Network Access](https://docs.atlas.mongodb.com/security/ip-access-list/)
- [Docker Best Practices](https://docs.docker.com/develop/dev-best-practices/)

---

¿Necesitas ayuda? Revisa los logs en EasyPanel o contacta con soporte.
