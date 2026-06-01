# AI Toolkit - All-in-One AI Content Generator SaaS

A complete SaaS platform for AI-powered content generation — text, images, and code — built with Laravel 11 + Vue 3 + Tailwind CSS.

## Features

- **Text Generation** — Blog posts, marketing copy, emails, social media, SEO content
- **Image Generation** — DALL-E 3 powered AI art, logos, illustrations
- **Code Generation** — Production-ready code in 15+ languages
- **40+ Pre-built Templates** — Blog Writer, Ad Copy, SEO Meta, Email Campaign, and more
- **Multi-Plan Billing** — Stripe integration with Free/Starter/Pro/Unlimited plans
- **Usage Tracking** — Per-user limits, monthly resets, real-time usage dashboard
- **Admin Panel** — User management, plan config, template builder, analytics
- **Multi-tenant Ready** — Each user has isolated usage, history, and billing

## Tech Stack

- **Backend:** Laravel 11, PHP 8.2+, MySQL 8, Redis
- **Frontend:** Vue 3, Pinia, Vue Router, Tailwind CSS 3
- **AI:** OpenAI API (GPT-4o, DALL-E 3)
- **Billing:** Stripe via Laravel Cashier
- **Auth:** Laravel Sanctum (token-based)

---

## Quick Install (Docker)

```bash
# 1. Clone and enter directory
git clone <your-repo> ai-toolkit && cd ai-toolkit

# 2. Start all services
docker compose up -d

# 3. Run installer
docker exec -it ai-toolkit-app bash install.sh

# 4. Configure .env
# Set OPENAI_API_KEY and STRIPE keys

# 5. Visit http://localhost:8000
```

## Quick Install (Manual)

```bash
# Requirements: PHP 8.2+, Composer, Node 20+, MySQL 8+

# 1. Install
git clone <your-repo> ai-toolkit && cd ai-toolkit
cp .env.example .env

# 2. Configure .env
# Set DB_*, OPENAI_API_KEY, STRIPE_* values

# 3. Run installer
bash install.sh

# 4. Start server
php artisan serve
```

## Default Admin Login

- **Email:** admin@aitoolkit.com
- **Password:** password

## Docker Services

| Service | Port | Description |
|---------|------|-------------|
| App | 8000 | Laravel + Vue application |
| MySQL | 3306 | Database |
| Redis | 6379 | Cache & queues |
| phpMyAdmin | 8080 | Database admin |

## API Endpoints

### Auth
- `POST /api/register` — Create account
- `POST /api/login` — Get token
- `GET /api/me` — Current user + usage

### Generation
- `POST /api/generate/text` — Generate text content
- `POST /api/generate/image` — Generate AI images
- `POST /api/generate/code` — Generate code
- `POST /api/generate/template/{id}` — Use a template

### History
- `GET /api/generations` — List history (filterable)
- `POST /api/generations/{id}/favorite` — Toggle favorite
- `DELETE /api/generations/{id}` — Delete

### Billing
- `GET /api/plans` — List plans
- `POST /api/subscribe` — Subscribe to plan
- `GET /api/invoices` — Invoice history

### Admin
- `GET /api/admin/dashboard` — Stats overview
- `GET /api/admin/users` — User management
- `POST /api/admin/templates` — Create template
- `PUT /api/admin/plans/{id}` — Update plan

## License

Regular License — use in a single end product for yourself or one client.
Extended License — use in a single SaaS product for multiple users.

## Support

Email: support@aitoolkit.com
Documentation: https://docs.aitoolkit.com
