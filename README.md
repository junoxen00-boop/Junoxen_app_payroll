# Junoxen PVT LTD - Employee Management & Payroll

This is the existing Junoxen Laravel Employee Dashboard extended with payroll while preserving the current Admin/Manager/Employee architecture and the existing Hostinger authentication database.

## Production database

The supplied live database dump identifies the production database as:

`u303244667_taskdashboard`

The production database already contains real users, bcrypt password hashes, roles, employees, departments, tasks, assignments, settings, notifications, sessions, cache and migration history. **Do not replace that data with development seed data.**

See `HOSTINGER_DEPLOYMENT.md` before any production deployment.

## Production safety

Use only:

```bash
php artisan migrate --force
```

for the live Hostinger database.

Never use these commands on production:

```bash
php artisan migrate:fresh
php artisan migrate:fresh --seed
php artisan db:wipe
php artisan migrate:reset
```

Development seeders are intentionally blocked when `APP_ENV=production`.

## Hostinger production configuration

Start from `.env.production.example` and copy only the appropriate settings into the existing production `.env`:

```env
APP_NAME="Junoxen PVT LTD"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://taskdashboard.junoxen.com

DB_CONNECTION=mysql
DB_HOST=HOSTINGER_DATABASE_HOST
DB_PORT=3306
DB_DATABASE=u303244667_taskdashboard
DB_USERNAME=HOSTINGER_DATABASE_USERNAME
DB_PASSWORD=HOSTINGER_DATABASE_PASSWORD

SESSION_DRIVER=database
CACHE_STORE=database
QUEUE_CONNECTION=database
```

Do not commit the real `.env`.

## Existing production authentication

The application continues using the existing `users.email`, `users.password` and `users.role_id` values. The payroll deployment does not replace live password hashes or production accounts.

The supplied live database has Admin, Manager and Employee role records and existing production users. Use those existing live credentials after connecting to Hostinger.

## Payroll access

- **Admin:** salary structure CRUD, payroll generation/editing, permitted draft deletion, cancellation, payment recording, reporting, CSV export, payslips and audit history.
- **Manager:** existing manager functions remain; no payroll financial administration.
- **Employee:** view and print only their own payroll. Server-side ownership checks reject other employee payroll records with HTTP 403.

## Payroll tables added

The production-safe payroll migrations add only:

- `employee_salary_structures`
- `payrolls`
- `payroll_payments`
- `payroll_audit_logs`

Payroll references the existing `employees.id` values.

## Local development

For isolated local development, `.env.example` uses a local MySQL database named `junoxen`.

```powershell
composer install
npm install
Copy-Item .env.example .env
php artisan key:generate
php artisan migrate:fresh --seed
npm run build
php artisan serve
```

`migrate:fresh --seed` is for a disposable local development database only.

## Verification

Production-safe checks:

```bash
php artisan optimize:clear
php artisan migrate:status
php artisan route:list
```

After confirming the migration list, deploy payroll schema with:

```bash
php artisan migrate --force
```

See:

- `HOSTINGER_DEPLOYMENT.md`
- `PAYROLL_CHANGES.md`
- `DATABASE_STRUCTURE.md`
- `HOSTINGER_MIGRATION_STATUS_FROM_DUMP.md`
