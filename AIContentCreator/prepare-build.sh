#!/bin/sh
# Script para copiar todas las carpetas del proyecto dentro de build-files

set -e

echo "📦 Preparando archivos para build de Docker..."

# Carpetas que deben copiarse (según tu captura)
DIRS="api code controllers db img models styles views"

# Archivos individuales a copiar (si los quieres)
FILES="index .env"

# Eliminar build anterior
rm -rf build-files
mkdir -p build-files

#########################################
# Copiar todas las carpetas del proyecto
#########################################
for dir in $DIRS; do
  if [ -d "$dir" ]; then
    echo "📁 Copiando carpeta: $dir"
    mkdir -p build-files/"$dir"
    cp -r "$dir"/* build-files/"$dir"/ 2>/dev/null || true
  else
    echo "⚠️ Carpeta no encontrada: $dir"
  fi
done

#########################################
# Copiar archivos sueltos del root
#########################################
for file in $FILES; do
  if [ -f "$file" ]; then
    echo "📄 Copiando archivo: $file"
    cp "$file" build-files/
  else
    echo "⚠️ Archivo no encontrado: $file"
  fi
done

echo "✅ Todos los archivos han sido copiados a build-files/"
