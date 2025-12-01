# Multi-stage build for optimized production image

# Stage 1: Build stage
FROM node:20-alpine AS builder

WORKDIR /app

# Copy package files from beta/node
COPY beta/node/package*.json ./

# Install dependencies
RUN npm install --production

# Stage 2: Production stage
FROM node:20-alpine

# Set working directory
WORKDIR /app

# Create non-root user for security
RUN addgroup -g 1001 -S nodejs && \
  adduser -S nodejs -u 1001

# Copy dependencies from builder
COPY --from=builder /app/node_modules ./node_modules

# Copy application files from beta/node
COPY --chown=nodejs:nodejs beta/node/*.js ./
COPY --chown=nodejs:nodejs beta/node/.env* ./

# Copy static files (HTML, CSS, JS, images) maintaining beta/ structure
# Usar --recursive o copiar directorios completos
COPY --chown=nodejs:nodejs beta/vistas/ ./vistas/
COPY --chown=nodejs:nodejs beta/css/ ./css/
COPY --chown=nodejs:nodejs beta/js/ ./js/
COPY --chown=nodejs:nodejs beta/img/ ./img/


# Verificar que los archivos se copiaron (debug) - ANTES de cambiar de usuario
RUN ls -la /app/ && \
    ls -la /app/vistas 2>/dev/null || echo "WARNING: vistas directory not found" && \
    ls -la /app/css 2>/dev/null || echo "WARNING: css directory not found" && \
    ls -la /app/img 2>/dev/null || echo "WARNING: img directory not found" && \
    echo "=== Verificación de archivos HTML ===" && \
    ls -la /app/vistas/*.html 2>/dev/null || echo "WARNING: No HTML files found"

# Switch to non-root user
USER nodejs

# Expose port - usar variable de entorno o 3000 por defecto
EXPOSE 3000

# Health check - usar PORT del environment o 3000
HEALTHCHECK --interval=30s --timeout=3s --start-period=5s --retries=3 \
  CMD node -e "const port = process.env.PORT || 3000; require('http').get(`http://localhost:${port}/health`, (r) => {process.exit(r.statusCode === 200 ? 0 : 1)})"

# Start the application
CMD ["node", "server.js"]
