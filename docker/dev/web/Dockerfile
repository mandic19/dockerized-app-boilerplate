# ==== CONFIGURE =====
# Use a Node base image
FROM node:alpine
# Set the working directory to /application inside the container
WORKDIR /application
# Copy app files
COPY ./web/package-lock.json ./
COPY ./web/package.json ./
COPY ./web/src ./src
COPY ./web/public ./public
# Install dependencies (npm ci makes sure the exact versions in the lockfile gets installed)
RUN npm ci
# ==== RUN =======
# Set the env to "development"
ENV NODE_ENV development
# Expose the port on which the app will be running (3000 is the default that `serve` uses)
EXPOSE 3000
# Install dependencies on host and start the app
CMD "node_modules" && npm start || npm ci && npm start


