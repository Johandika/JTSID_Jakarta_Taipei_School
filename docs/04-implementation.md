# MVP Implementation Decisions

## Confirmed stack

The latest user direction supersedes the legacy PDF's MySQL, Bootstrap, and jQuery references: Laravel + PostgreSQL + Tailwind CSS. Implemented with Laravel 13, Livewire 4, and Filament 5. The version requirements were checked against the [Laravel documentation](https://laravel.com/docs/13.x/installation) and [Filament installation documentation](https://filamentphp.com/docs/5.x/introduction/installation). Exact installed versions are locked in Composer and npm lockfiles.

## Resolved assumptions

- MVP means a minimum viable product with Laravel's standard MVC folders, not a custom Model-View-Presenter framework.
- TK/SD/SMP/SMA are grade-level filters. The six resource types have individual URLs.
- Academics is one page with sections for Mandarin, Indonesian/Pancasila/Religions, and English.
- Admissions contains introductory steps and Q&A; short-term Mandarin has a separate managed page. Admissions gallery is a configurable URL.
- Slideshow replacements keep the same record/topic and update its image reference. At most ten records may be active, enforced within a PostgreSQL advisory-lock transaction. Old files remain stored rather than deleting media that might still be referenced.
- Congratulations popups are omitted until the school specifies the intended event/content. University acceptance records are available without unsolicited popups.
- English URLs have no prefix; Indonesian uses `/id`; Traditional Chinese uses `/zh` with locale `zh-TW`.
- A missing translation falls back to the complete English row. Content editors see completeness for each locale.
- Session lifetime is 60 minutes. Public users do not have registration/accounts. Filament login has built-in rate limiting and CSRF protection.

## ERD adaptations

- PostgreSQL uses UTF-8, native booleans, timestamps, JSONB audit field names, foreign keys, and composite indexes. MySQL-specific collations and `longtext` were replaced appropriately.
- Laravel's standard `users.password` is hashed. Four roles are relational records via `role_id`; permissions use explicit Laravel policies rather than an editable permissions table for this MVP.
- English, Indonesian, and Traditional Chinese remain separate translation rows, with unique `(parent_id, locale)` constraints. Pages, posts, and resources use consistent `title`, `summary`, `body`, and SEO fields.
- One document path per news/resource record and direct image paths on their owning records simplify the one-file MVP workflow. A separate shared media library and polymorphic attachments are deferred until multi-file/library reuse is needed. Storage paths are relative, documents are private, and uploaded filenames are generated.
- Menus use validated internal paths or HTTP(S) URLs, a parent relationship, and translation rows. Maximum two visible navigation levels are supported.
- Calendar is implemented as a resource type with downloadable documents. A separate interactive calendar-event module is outside this MVP.
- Staff and university records retain proper names and descriptions as entered; their navigation/filter UI is localized. Full multilingual staff biography fields can be added if required.

## Content and provenance

All seeded news, university outcomes, staff identities, and documents are **test data**, not official records. Sample content is marked in article bodies and the local footer. The original site's complete content and archive have not been migrated; school-approved copy and translations should replace the samples through the CMS.

Photographs and logo are retained from the school website for this requested redesign:

- Logo: https://www.jtsid.org/img/logo.jpg
- Campus: https://www.jtsid.org/img/information/information03_01.jpg
- Library: https://www.jtsid.org/img/information/information03_04.jpg
- Sports court: https://www.jtsid.org/img/information/information03_08.jpg
- Existing highlight: https://www.jtsid.org/archive/banner/83.jpg
- Source gallery: https://www.jtsid.org/infomation03.php
- WhatsApp and Instagram links were read from https://www.jtsid.org/ and used to generate local PNG QR codes.

TikTok is not seeded because an official destination has not been verified. The admin supports adding it and uploading its QR. Address is intentionally at district/city level until the exact approved postal address is supplied. The telephone follows the project sitemap.

## Local operations

The application uses only databases `jtsid` and `jtsid_test` on the existing PostgreSQL container. No existing database was modified. Dependency volumes are separate from the source and uploaded content. Uploads persist under `storage/app`; the app is bound to loopback on port 8000.

For backups, use `pg_dump` on `jtsid` and back up `storage/app`. Do not use `migrate:fresh` on the local content database to restart normal development. `scripts/start.ps1` is designed to preserve existing records and passwords.
