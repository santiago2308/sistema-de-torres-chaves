FROM node:22-alpine AS build

WORKDIR /app

COPY frontend/package.json frontend/package-lock.json ./
RUN npm ci

COPY frontend ./

ARG VITE_API_URL
ARG VITE_WHATSAPP_NUMBER
ENV VITE_API_URL=${VITE_API_URL}
ENV VITE_WHATSAPP_NUMBER=${VITE_WHATSAPP_NUMBER}

RUN npm run build

# Etapa final: solo los estáticos compilados, servidos por Nginx
FROM nginx:1.27-alpine AS final

COPY --from=build /app/dist /usr/share/nginx/html

EXPOSE 80

CMD ["nginx", "-g", "daemon off;"]