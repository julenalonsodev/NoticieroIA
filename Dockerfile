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
COPY --chown=nodejs:nodejs beta/vistas ./vistas
COPY --chown=nodejs:nodejs beta/css ./css
COPY --chown=nodejs:nodejs beta/js ./js
COPY --chown=nodejs:nodejs beta/img ./img

# Switch to non-root user
USER nodejs

# Expose port
EXPOSE 3000

# Health check
HEALTHCHECK --interval=30s --timeout=3s --start-period=5s --retries=3 \
  CMD node -e "require('http').get('http://localhost:3000/health', (r) => {process.exit(r.statusCode === 200 ? 0 : 1)})"

# Start the application
CMD ["node", "server.js"]
