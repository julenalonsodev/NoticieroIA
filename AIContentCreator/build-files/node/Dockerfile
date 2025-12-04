# Multi-stage build for optimized production image
# IMPORTANTE: Este Dockerfile requiere que el contexto de build sea la RAÍZ del proyecto
# para poder copiar los archivos estáticos desde beta/views, beta/css, etc.

# Stage 1: Build stage
FROM node:20-alpine AS builder

WORKDIR /app

# Copy package files
COPY ./package*.json ./

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

# Copy application files from beta/node
COPY --chown=nodejs:nodejs ./*.js ./
COPY --chown=nodejs:nodejs ./.env* ./

# Copy static files from beta/ directory
COPY --chown=nodejs:nodejs ./api/ ./api/
COPY --chown=nodejs:nodejs ./code/ ./code/
COPY --chown=nodejs:nodejs ./controllers/ ./controllers/
COPY --chown=nodejs:nodejs ./db/ ./db/
COPY --chown=nodejs:nodejs ./img/ ./img/
COPY --chown=nodejs:nodejs ./models/ ./models/
COPY --chown=nodejs:nodejs ./styles/ ./styles/
COPY --chown=nodejs:nodejs ./views/ ./views/

# Switch to non-root user
USER nodejs

# Expose port
EXPOSE 3000

# Health check
HEALTHCHECK --interval=30s --timeout=3s --start-period=5s --retries=3 \
  CMD node -e "require('http').get('http://localhost:3000/health', (r) => {process.exit(r.statusCode === 200 ? 0 : 1)})"

# Start the application
CMD ["node", "server.js"]
