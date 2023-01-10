FROM node:alpine AS builder
# Set work directory
WORKDIR /application
# Copy app files
COPY ./web/package.json ./
COPY ./web/package-lock.json ./
COPY ./web/src ./src
COPY ./web/public ./public
# Install dependencies (npm ci makes sure the exact versions in the lockfile gets installed)
RUN npm ci --production
# Build the app
RUN npm run build
# Bundle static assets with nginx
FROM nginx:alpine
ENV NODE_ENV production
# Copy built assets from `builder` image
COPY --from=builder /application/build /usr/share/nginx/html
# Expose port
EXPOSE 80
# Start nginx
CMD ["nginx", "-g", "daemon off;"]
