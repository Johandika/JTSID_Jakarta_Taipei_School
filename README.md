# Jakarta Taipei School CMS

Laravel 13, Blade, Livewire 4, Tailwind CSS 4, Filament 5, and PostgreSQL 16.

## Try locally

The running application is available at:

- Public website: http://localhost:8000
- Admin panel: http://localhost:8000/admin
- Indonesian: http://localhost:8000/id
- Traditional Chinese: http://localhost:8000/zh

Local demo accounts, all using password `JtsLocal2026!`:

| Email | Access |
| --- | --- |
| `admin@jts.local` | Super admin, including users |
| `editor@jts.local` | All content and website settings |
| `academic@jts.local` | Academic department content only |
| `viewer@jts.local` | Read-only lists |

These accounts are seeded only in local/testing environments. Demo stories, staff, admissions, university acceptances, and PDF files are sample data, not official school records. Existing school photographs and social links are attributed in `docs/04-implementation.md`.

## Start / stop

Prerequisites: Docker Desktop with Linux containers, Node.js 22+, npm, and the existing PostgreSQL container named `postgres` on port 5432.

```powershell
# First setup or repeat setup (preserves existing content)
.\scripts\start.ps1

# Subsequent starts
docker compose up -d

# Stop this application; PostgreSQL and its other databases remain running
docker compose stop
```

The setup creates the dedicated databases `jtsid` and `jtsid_test` and the role `jtsid_app` if missing. It builds PHP, installs locked dependencies, migrates, seeds on first use, builds frontend assets, and starts the server. PHP and Composer do not need to be installed on Windows.

The `.env` connection uses `host.docker.internal:5432` from Docker. If running PHP directly on Windows, change `DB_HOST` to `127.0.0.1`. The default app port is 8000. PostgreSQL credentials for this local app are in `.env.example`; `.env` itself is ignored by Git.

## Development

```powershell
docker compose exec app php artisan migrate
docker compose exec app php artisan db:seed
npm run build
docker compose exec app vendor/bin/pint
```

Rebuilding assets is enough for local testing; no Vite server is required. For live CSS/JS changes, run `npm run dev` in a terminal. Laravel serves the current PHP/Blade files through the mounted workspace. Composer dependencies and compiled Blade views live in Docker volumes for faster Windows filesystem performance.

The local PHP server uses four workers. After changing `.env`, run `docker compose restart app`.

## Image version

Set the Docker image version in `.env`:

```env
APP_VERSION=1.0.2
```

The local compose file tags builds as `johandika/jtsid:${APP_VERSION}`. Build and push the same version with PowerShell:

```powershell
$env:APP_VERSION = (Get-Content .env | Select-String '^APP_VERSION=').ToString().Split('=')[1].Trim()
docker build -f Dockerfile.prod -t "johandika/jtsid:$env:APP_VERSION" .
docker push "johandika/jtsid:$env:APP_VERSION"
```

For production, use the same `APP_VERSION` value in the production env file so `docker compose pull` gets the intended image tag.

## Verification

```powershell
docker compose exec app php artisan test --compact
node tests/browser/inspect.mjs
node tests/browser/journeys.mjs
```

PHP tests use **only `jtsid_test`**, with migrations and transaction isolation. They cover public routes/locales, publication visibility, translations, department policies, CMS forms, document uploads/downloads, and the slideshow limit. Browser checks use installed Chrome in headless mode and save screenshots to `test-results/`.

## Structure

```text
app/
  Filament/Resources/   CMS resources and create/edit/list pages
  Filament/Support/     Shared translation, upload, and publishing fields
  Filament/Widgets/     Admin dashboard counts
  Http/Controllers/    Public pages, listings, and protected downloads
  Http/Middleware/     Public locale selection
  Livewire/            Search, filters, sort, and pagination
  Models/              Eloquent models and relational translations
  Policies/            Global, department, and user permissions
  Support/             Locale-aware links and media URLs
database/
  migrations/          PostgreSQL schema
  seeders/             Reference data and guarded local demo content
resources/
  css/                 Tailwind styles
  js/                  Lucide icons and slideshow behavior
  views/               Public Blade pages and shared components
lang/                  English, Indonesian, Traditional Chinese labels
tests/                 Feature tests and browser journeys
scripts/               Repeatable local startup
docs/                  PRD, ERD, sitemap, implementation decisions
```

This MVP follows Laravel's MVC structure. Publishing, relationships, and authorization stay on the server. Filament is the admin interface; public layouts do not import the admin stylesheet.

## Content management

- Create or edit news/pages/resources, then add English and optional Indonesian/Traditional Chinese translations in the same record. Missing translations are visible in the CMS list.
- Published content is public only when its English title exists and its publish date is not in the future. Missing locales fall back to the complete English translation.
- Documents: PDF, DOC, DOCX, RAR; maximum 15 MB; private storage and a publication-aware download handler.
- Images: JPEG, PNG, WebP. Highlights: maximum 1 MB and 10 active slides. Editing a slide replaces its image reference. Inactive slides are kept for reuse.
- Page hero/student-work images, navigation, contacts/QR, staff, university acceptances, departments, and academic years can be edited in the admin panel.
- Roles are fixed for the MVP and assigned by super admins. Department ownership is enforced by both policies and form/model validation. Audit logs are read-only.

See `docs/04-implementation.md` for assumptions, ERD adaptations, and content still awaiting school approval.
