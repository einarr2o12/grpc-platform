FROM node:18-alpine

# Install curl for health checks
RUN apk add --no-cache curl

WORKDIR /app

# Copy package files
COPY ./package*.json ./

# Install dependencies
RUN npm ci --production

# Copy source code
COPY ./ .

# Copy proto files
COPY proto/ ./proto/

# Create non-root user
RUN addgroup -g 1001 -S nodejs
RUN adduser -S nodeuser -u 1001

# Change ownership of the app directory
RUN chown -R nodeuser:nodejs /app
USER nodeuser

# Expose gRPC port only
EXPOSE 50052

CMD ["npm", "start"]