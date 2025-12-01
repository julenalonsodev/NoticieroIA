#!/bin/sh
# Script para copiar archivos estáticos durante el build de Nixpacks

echo "📂 Copiando archivos estáticos..."

# Crear directorios si no existen
mkdir -p ./vistas ./css ./js ./img

# Copiar archivos desde el directorio padre
if [ -d "../vistas" ]; then
  echo "✅ Copiando vistas..."
  cp -r ../vistas/* ./vistas/ 2>/dev/null || echo "⚠️  No se pudieron copiar vistas"
else
  echo "⚠️  Directorio vistas no encontrado"
fi

if [ -d "../css" ]; then
  echo "✅ Copiando CSS..."
  cp -r ../css/* ./css/ 2>/dev/null || echo "⚠️  No se pudo copiar CSS"
else
  echo "⚠️  Directorio css no encontrado"
fi

if [ -d "../js" ]; then
  echo "✅ Copiando JS..."
  cp -r ../js/* ./js/ 2>/dev/null || echo "⚠️  No se pudo copiar JS"
else
  echo "⚠️  Directorio js no encontrado"
fi

if [ -d "../img" ]; then
  echo "✅ Copiando imágenes..."
  cp -r ../img/* ./img/ 2>/dev/null || echo "⚠️  No se pudieron copiar imágenes"
else
  echo "⚠️  Directorio img no encontrado"
fi

echo "📋 Verificando archivos copiados:"
ls -la vistas/ 2>/dev/null || echo "vistas no encontrado"
ls -la css/ 2>/dev/null || echo "css no encontrado"
ls -la js/ 2>/dev/null || echo "js no encontrado"
ls -la img/ 2>/dev/null || echo "img no encontrado"

echo "✅ Proceso de copia completado"

