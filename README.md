# Catálogo de Servicios con Chatbot

Sistema web de catálogo de servicios de sonido, iluminación y tecnología para eventos, con un chatbot integrado en la misma página que guía a los usuarios sobre servicios, precios y contacto. Incluye acceso directo a WhatsApp.

Soporta una concurrencia media de **1000 usuarios simultáneos** mediante caché con Redis, colas, paginación y Nginx como reverse proxy.

## Stack tecnológico

| Capa | Tecnología |
|---|---|
| Frontend | React 19 + TypeScript + Vite + Vitest |
| Backend | PHP 8.2 + Laravel 12 + Sanctum + Pest |
| Base de datos | PostgreSQL 16 (Docker) / SQLite (desarrollo local) |
| Caché y colas | Redis |
| Infraestructura | Docker Compose, Nginx, PHP-FPM |
| Control de versiones | Git |

## Estructura del proyecto

```text
catalogo-chatbot/
├── backend/               → API REST Laravel
│   ├── app/Models/        → Service, ChatSession, ChatMessage
│   ├── app/Http/Controllers/Api/ → ServiceController, ChatController
│   ├── app/Services/      → ChatbotService (motor de reglas)
│   ├── app/Http/Resources/ → ServiceResource
│   ├── database/migrations/ → esquema de tablas
│   ├── database/seeders/  → 10 servicios reales
│   ├── routes/api.php     → endpoints
│   └── tests/             → pruebas Pest
├── frontend/              → SPA React
│   └── src/
│       ├── api.ts         → cliente HTTP (servicios + chat)
│       ├── components/    → ServiceCatalog, ServiceCard, ChatWidget, WhatsAppButton
│       └── tests          → pruebas Vitest
├── docker/                → Dockerfiles (app, frontend) + config Nginx
├── docker-compose.yml     → orquestación: app, nginx, postgres, redis, worker, migrate
└── .env.example           → plantilla de variables (sin secretos)
```

## Módulos del sistema

### 1. Módulo Servicios
- Modelo `Service` + migración con columnas `name`, `category`, `description`, `price`, `is_active`.
- Índices sobre `category` y `is_active` para consultas rápidas.
- Seeder con 10 servicios reales (sonido, iluminación, DJ, packs, pantalla LED, efectos).
- El precio se formatea automáticamente (`price_formatted` → `280.000`).

### 2. Módulo API (catálogo)
- `GET /api/v1/services` → listado paginado (12 por página) con filtro `?category=`.
- `GET /api/v1/services/{id}` → detalle de un servicio.
- Respuestas en JSON vía `ServiceResource`.
- **Caché en Redis** (10 min) para evitar golpes a PostgreSQL y aguantar alta concurrencia.
- **Rate limiting** por IP: 120 req/min en lecturas.
- CORS habilitado para `api/*`.

### 3. Módulo Chatbot
- Widget flotante en el frontend (mismo archivo `App.tsx`).
- **Motor de reglas propio** (`ChatbotService`): detecta intenciones por palabras clave:
  - saludo, servicios, precios, categorías, reservas, ubicación, horarios, WhatsApp, despedida.
- Detecta el **nombre exacto de un servicio** para responder su precio e incluidos.
- Siempre ofrece derivar a WhatsApp cuando es relevante (`show_whatsapp`).
- Persistencia: `chat_sessions` + `chat_messages` para historial por visitante.
- `POST /api/v1/chat/message` (rate limit 30/min) y `GET /api/v1/chat/history/{sessionId}`.
- Recomendaciones de preguntas rápidas para el usuario.

### 4. Módulo WhatsApp
- Botón "Contáctanos por WhatsApp" (hero, sección de contacto y dentro del chat).
- Enlace `https://wa.me/{número}` con mensaje predefinido.
- El número se configura en `.env` (`WHATSAPP_NUMBER`), **no** está hardcodeado.

### 5. Módulo Infraestructura (Docker + Nginx)
- `docker-compose.yml` con 6 servicios:
  - `app` (PHP-FPM + Laravel) — lógica de negocio.
  - `nginx` — sirve la SPA, proxies `/api` al backend, HTTPS, estáticos con caché.
  - `postgres` — base de datos con healthcheck.
  - `redis` — caché/colas/sesiones con persistencia.
  - `worker` — cola de tareas en segundo plano.
  - `migrate` — corre migraciones + seed automáticamente.
- Escala para 1000 usuarios: caché Redis, rate limiting, paginación e índices.

### 6. Módulo Seguridad
- Variables de entorno para todos los secretos (`.env` no se sube).
- Contraseñas con hashing (Laravel) — login opcional con Sanctum.
- Validación de entrada en backend, protección SQL injection vía Eloquent.
- `APP_DEBUG=false` en producción, logs de errores sin datos sensibles.

### 7. Módulo Testing
- Backend: Pest — 10 tests (API catálogo, filtros, 404, chatbot, historial, validación).
- Frontend: Vitest + Testing Library — render de tarjetas y enlace de WhatsApp.
- Comandos: `php artisan test` y `npm test`.

## Requisitos

- PHP 8.2+, Composer, Node 20+
- Docker + Docker Compose (para el entorno de producción)
- El número de WhatsApp en formato internacional (ej. `5492646214599`)

## Instalación local (desarrollo con SQLite)

Backend:

```bash
cd backend
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate --seed
php artisan serve --port=8000
```

Frontend:

```bash
cd frontend
npm install
cp .env.example .env   # configurar VITE_API_URL y VITE_WHATSAPP_NUMBER
npm run dev
```

Abrir `http://localhost:5173`.

## Configuración con Docker (producción)

1. Copiar plantilla y generar clave:

```bash
cp .env.example .env
cd backend && php artisan key:generate --show
```

2. Poner la clave en `.env` (`APP_KEY=base64:...`), ajustar `DB_PASSWORD`, `WHATSAPP_NUMBER` y `VITE_WHATSAPP_NUMBER`.

3. Levantar:

```bash
docker compose up -d --build
docker compose exec app php artisan migrate --seed
```

4. Acceder a `http://localhost` (el contenedor `migrate` ya aplica migraciones y seed automáticamente al primer arranque).

## Variables de entorno principales

| Variable | Descripción |
|---|---|
| `APP_KEY` | Clave de cifrado de Laravel (generar con `artisan key:generate`) |
| `DB_*` | Credenciales de PostgreSQL |
| `CACHE_STORE=redis` | Caché distribuida |
| `QUEUE_CONNECTION=redis` | Colas |
| `SESSION_DRIVER=redis` | Sesiones |
| `WHATSAPP_NUMBER` | Número de WhatsApp (formato internacional, sin `+`) |
| `VITE_API_URL` | URL base de la API (en producción `/api/v1`) |
| `VITE_WHATSAPP_NUMBER` | Número de WhatsApp del frontend |

## Endpoints de la API

| Método | Ruta | Descripción | Auth |
|---|---|---|---|
| GET | `/api/v1/services` | Lista de servicios (paginada, filtrable) | No |
| GET | `/api/v1/services/{id}` | Detalle de servicio | No |
| POST | `/api/v1/chat/message` | Enviar mensaje al chatbot | No |
| GET | `/api/v1/chat/history/{sessionId}` | Historial de la sesión | No |

## Testing

```bash
# Backend
cd backend && php artisan test

# Frontend
cd frontend && npm test
```

## Despliegue recomendado

- VPS Linux/Ubuntu con Docker Compose.
- Nginx expone puertos 80/443; añadir HTTPS con Let's Encrypt.
- Para más trafico: aumentar réplicas de `app` detrás del load balancer y recursos del VPS.