# Multi-stage build for optimized production image
# IMPORTANTE: Este Dockerfile requiere que el contexto de build sea la RAÍZ del proyecto
# para poder copiar los archivos estáticos desde AIContentCreator/views, AIContentCreator/css, etc.

# Stage 1: Build stage
FROM node:20-alpine AS builder

WORKDIR /app

# Copy package files
COPY AIContentCreator/node/package*.json ./

# Install dependencies
RUN npm ci --only=production

# Stage 2: Production stage
FROM node:20-alpine

# Set working directory
WORKDIR /app

# Create non-root user for security
RUN addgroup -g 1001 -S nodejs && \
    adduser -S nodejs -u 1001

# Copy dependencies from builder
COPY --from=builder /app/node_modules ./node_modules

# Copy application files from AIContentCreator/node
COPY --chown=nodejs:nodejs AIContentCreator/node/*.js ./
COPY --chown=nodejs:nodejs AIContentCreator/node/.env* ./

# Copy static files from AIContentCreator/ directory
COPY --chown=nodejs:nodejs AIContentCreator/views/ ./views/
COPY --chown=nodejs:nodejs AIContentCreator/css/ ./css/
COPY --chown=nodejs:nodejs AIContentCreator/js/ ./js/
COPY --chown=nodejs:nodejs AIContentCreator/img/ ./img/
COPY --chown=nodejs:nodejs AIContentCreator/fonts/ ./fonts/
COPY --chown=nodejs:nodejs AIContentCreator/data/ ./data/
COPY --chown=nodejs:nodejs AIContentCreator/templates/ ./templates/
COPY --chown=nodejs:nodejs AIContentCreator/scripts/ ./scripts/
COPY --chown=nodejs:nodejs AIContentCreator/config/ ./config/
COPY --chown=nodejs:nodejs AIContentCreator/node/public/ ./public/
COPY --chown=nodejs:nodejs AIContentCreator/node/models/ ./models/
COPY --chown=nodejs:nodejs AIContentCreator/node/routes/ ./routes/
COPY --chown=nodejs:nodejs AIContentCreator/node/controllers/ ./controllers/
COPY --chown=nodejs:nodejs AIContentCreator/node/middleware/ ./middleware/
COPY --chown=nodejs:nodejs AIContentCreator/node/utils/ ./utils/

# Switch to non-root user
USER nodejs

# Expose port
EXPOSE 3000

# Health check
HEALTHCHECK --interval=30s --timeout=3s --start-period=5s --retries=3 \
  CMD node -e "require('http').get('http://localhost:3000/health', (r) => {process.exit(r.statusCode === 200 ? 0 : 1)})"

# Start the application
CMD ["node", "server.js"]
