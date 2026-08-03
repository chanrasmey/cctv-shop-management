# CCTV Shop ERP
## Deployment Guide

Version: v0.9.1

---

# System Requirements

- PHP 8.5
- Composer
- Node.js (LTS)
- NPM
- MySQL 8+
- Git

---

# Clone Repository

```bash
git clone https://github.com/chanrasmey/cctv-shop-management.git

cd cctv-shop-management
```

---

# Install Dependencies

```bash
composer install

npm install
```

---

# Environment

Copy

```
.env.example
```

to

```
.env
```

Generate key

```bash
php artisan key:generate
```

---

# Database

Configure

```
DB_HOST

DB_PORT

DB_DATABASE

DB_USERNAME

DB_PASSWORD
```

Run

```bash
php artisan migrate

php artisan db:seed
```

---

# Build Frontend

```bash
npm run build
```

For development

```bash
npm run dev
```

---

# Storage

```bash
php artisan storage:link
```

---

# Cache

```bash
php artisan optimize

php artisan config:cache

php artisan route:cache

php artisan view:cache
```

---

# Validation

Before deployment

```bash
composer dump-autoload

php artisan optimize:clear

php artisan route:list

php artisan test
```

---

# Production Checklist

☐ Environment configured

☐ Database migrated

☐ Storage linked

☐ Frontend built

☐ Tests passed

☐ Company settings configured

☐ Administrator account created

☐ Backup configured

☐ Deployment verified

---

# Upgrade Procedure

1. Backup database.
2. Pull latest code.
3. Run Composer.
4. Run migrations.
5. Build frontend.
6. Clear caches.
7. Verify application.

---

# Backup Recommendation

Daily

- Database backup

Weekly

- Storage backup

Monthly

- Full application backup