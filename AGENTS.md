# AGENTS.md — Catálogo de Servicios con Chatbot

Guía para agentes de IA y desarrolladores que trabajan en este repositorio. Complementa el AGENTS.md de referencia del cliente (stack: React+TS, Laravel, PostgreSQL, Redis, Docker, Nginx).

## Stack

- **Frontend:** React + TypeScript + Vite + Vitest
- **Backend:** PHP + Laravel 12 + Sanctum + Pest
- **BD:** PostgreSQL (Docker) / SQLite (desarrollo local)
- **Caché/colas:** Redis
- **Infra:** Docker Compose, Nginx, PHP-FPM

## Estructura

```text
backend/    → API REST Laravel (app/Models, app/Services, app/Http/Controllers/Api, routes/api.php)
frontend/   → SPA React (src/api.ts, src/components, src/types.ts)
docker/     → Dockerfiles + nginx/default.conf
docker-compose.yml
.env.example
```

## Reglas

1. **Cambios mínimos:** resolver el problema con el cambio más pequeño; no refactorizar módulos completos.
2. **Eloquent, no SQL manual:** usar Eloquent salvo que se justifique.
3. **No hardcodear secretos:** todo secreto en `.env`; `.env` nunca al repo.
4. **Validar siempre en backend:** no confiar en datos del cliente.
5. **Mantener compatibilidad** con el código existente y la estructura actual.
6. **No eliminar** archivos o funcionalidades sin autorización.
7. **Rate limiting y caché** en endpoints públicos (concurrencia ~1000 usuarios).
8. **Tests:** backend con Pest, frontend con Vitest; correr antes de cambios importantes.

## Convenciones

- Precios se almacenan como enteros (COP) y se formatean con `price_formatted`.
- Categorías fijas: `Sonido`, `Iluminación`, `DJ`, `Pantalla LED`, `Efectos`, `Packs`.
- Rutas API bajo prefijo `/api/v1`.
- El chatbot responde con estructura `{ message, show_whatsapp }`.

## Comandos frecuentes

```bash
# Backend
cd backend
composer install
php artisan migrate --seed
php artisan serve --port=8000
php artisan test

# Frontend
cd frontend
npm install
npm run dev
npm test
npm run build

# Producción
docker compose up -d --build
```

## Notas de despliegue

- `docker compose up` levanta app, nginx, postgres, redis, worker y migrate (auto-seed).
- HTTPS con Let's Encrypt; Nginx proxya `/api` al contenedor `app`.