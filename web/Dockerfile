# ==== CONFIGURE =====
# Use a Node 16 base image
FROM node:16-alpine 
# Set the working directory to /application inside the container
WORKDIR /application
# Copy app files
COPY ./package-lock.json ./
COPY ./package.json ./
COPY ./src ./src
COPY ./public ./public
# Install dependencies (npm ci makes sure the exact versions in the lockfile gets installed)
RUN npm ci
# ==== RUN =======
# Set the env to "development"
ENV NODE_ENV development
# Expose the port on which the app will be running (3000 is the default that `serve` uses)
EXPOSE 3000
# Install dependencies on host and start the app
CMD "node_modules" && npm start || npm ci && npm start

