#!/bin/bash
# Script de verificación para el despliegue en EasyPanel

echo "🔍 Verificando proyecto NoticieroIA para despliegue..."
echo ""

ERRORS=0

# Verificar estructura de directorios
echo "📁 Verificando estructura de directorios:"
for dir in "beta/node" "beta/vistas" "beta/css" "beta/js" "beta/img"; do
    if [ -d "$dir" ]; then
        echo "  ✅ $dir existe"
    else
        echo "  ❌ $dir NO existe"
        ERRORS=$((ERRORS + 1))
    fi
done
echo ""

# Verificar archivos críticos
echo "📄 Verificando archivos críticos:"
[ -f "beta/node/server.js" ] && echo "  ✅ server.js existe" || (echo "  ❌ server.js NO existe" && ERRORS=$((ERRORS + 1)))
[ -f "beta/node/db.js" ] && echo "  ✅ db.js existe" || (echo "  ❌ db.js NO existe" && ERRORS=$((ERRORS + 1)))
[ -f "beta/node/package.json" ] && echo "  ✅ package.json existe" || (echo "  ❌ package.json NO existe" && ERRORS=$((ERRORS + 1)))
[ -f "Dockerfile.simple" ] && echo "  ✅ Dockerfile.simple existe" || (echo "  ❌ Dockerfile.simple NO existe" && ERRORS=$((ERRORS + 1)))
echo ""

# Verificar archivos HTML
echo "🌐 Verificando archivos HTML:"
for html in "beta/vistas/login.html" "beta/vistas/home.html" "beta/vistas/articulos.html"; do
    if [ -f "$html" ]; then
        echo "  ✅ $(basename $html) existe"
    else
        echo "  ❌ $(basename $html) NO existe"
        ERRORS=$((ERRORS + 1))
    fi
done
echo ""

# Verificar archivos estáticos referenciados
echo "🎨 Verificando archivos estáticos:"
[ -f "beta/css/styles.css" ] && echo "  ✅ styles.css existe" || (echo "  ❌ styles.css NO existe" && ERRORS=$((ERRORS + 1)))
[ -f "beta/css/home.css" ] && echo "  ✅ home.css existe" || (echo "  ❌ home.css NO existe" && ERRORS=$((ERRORS + 1)))
[ -f "beta/css/noticias.css" ] && echo "  ✅ noticias.css existe" || (echo "  ❌ noticias.css NO existe" && ERRORS=$((ERRORS + 1)))
[ -f "beta/js/home.js" ] && echo "  ✅ home.js existe" || (echo "  ❌ home.js NO existe" && ERRORS=$((ERRORS + 1)))
[ -f "beta/js/generos.js" ] && echo "  ✅ generos.js existe" || (echo "  ❌ generos.js NO existe" && ERRORS=$((ERRORS + 1)))
[ -f "beta/img/logo.jpg" ] && echo "  ✅ logo.jpg existe" || (echo "  ❌ logo.jpg NO existe" && ERRORS=$((ERRORS + 1)))
echo ""

# Verificar sintaxis JavaScript
echo "🔧 Verificando sintaxis JavaScript:"
if command -v node &> /dev/null; then
    cd beta/node
    if node -c server.js 2>/dev/null && node -c db.js 2>/dev/null; then
        echo "  ✅ Sintaxis de JavaScript correcta"
        cd ../..
    else
        echo "  ❌ Error de sintaxis en archivos JavaScript"
        ERRORS=$((ERRORS + 1))
        cd ../..
    fi
else
    echo "  ⚠️  Node.js no disponible para verificar sintaxis"
fi
echo ""

# Resumen final
echo "═══════════════════════════════════════"
if [ $ERRORS -eq 0 ]; then
    echo "✅ VERIFICACIÓN COMPLETA - Todo está correcto"
    echo "🚀 Listo para desplegar en EasyPanel"
    exit 0
else
    echo "❌ VERIFICACIÓN FALLIDA - Se encontraron $ERRORS error(es)"
    echo "⚠️  Corrige los errores antes de desplegar"
    exit 1
fi

