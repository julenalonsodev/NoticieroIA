#!/bin/sh
# Script para preparar los archivos necesarios para el build de Docker
# cuando el contexto de build es AIContentCreator
#
# Ahora copia los archivos desde las carpetas locales de AIContentCreator
# (node de beta, views, styles, code, img) a build-files/

set -e

echo "📦 Preparando archivos para build de Docker desde AIContentCreator..."

# Limpia y crea directorios necesarios en AIContentCreator
rm -rf build-files
mkdir -p build-files/node build-files/vistas build-files/css build-files/js build-files/img

#########################
# 1) Backend Node (de beta, de momento)
#########################
if [ -d "../beta/node" ]; then
  echo "✅ Copiando archivos de Node.js desde ../beta/node..."
  cp -r ../beta/node/* build-files/node/ 2>/dev/null || echo "⚠️  Error copiando archivos de Node.js"
else
  echo "❌ Error: Directorio ../beta/node no encontrado"
  exit 1
fi

#########################
# 2) Vistas (desde AIContentCreator/views -> build-files/vistas/*.html)
#########################
if [ -d "./views" ]; then
  echo "✅ Generando vistas HTML desde ./views..."
  for view in ./views/*_view.phtml; do
    [ -f "$view" ] || continue
    base="$(basename "$view")"
    name="${base%_view.phtml}"       # ej: home_view.phtml -> home
    cp "$view" "build-files/vistas/${name}.html"
  done
else
  echo "⚠️  Directorio ./views no encontrado; no se generan vistas"
fi

#########################
# 3) CSS (desde AIContentCreator/styles)
#########################
if [ -d "./styles" ]; then
  echo "✅ Copiando CSS desde ./styles..."
  cp -r ./styles/* build-files/css/ 2>/dev/null || echo "⚠️  CSS no encontrado en ./styles"
else
  echo "⚠️  Directorio ./styles no encontrado; no se copiará CSS"
fi

#########################
# 4) JS (desde AIContentCreator/code)
#########################
if [ -d "./code" ]; then
  echo "✅ Copiando JS desde ./code..."
  cp -r ./code/* build-files/js/ 2>/dev/null || echo "⚠️  JS no encontrado en ./code"
else
  echo "⚠️  Directorio ./code no encontrado; no se copiará JS"
fi

#########################
# 5) Imágenes (desde AIContentCreator/img)
#########################
if [ -d "./img" ]; then
  echo "✅ Copiando imágenes desde ./img..."
  cp -r ./img/* build-files/img/ 2>/dev/null || echo "⚠️  Imágenes no encontradas en ./img"
else
  echo "⚠️  Directorio ./img no encontrado; no se copiarán imágenes"
fi

echo "✅ Preparación completada usando AIContentCreator"
