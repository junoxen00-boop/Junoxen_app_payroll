# Junoxen Hostinger Production Deployment

This project is prepared to add the payroll module to the existing Hostinger database **without replacing the current users, passwords, employees, roles, tasks, or IDs**.

## Live database verified from the supplied dump

- Database: `u303244667_taskdashboard`
- Server type in the supplied dump: MariaDB 11.8.9
- Existing authentication tables: `users`, `roles`, `sessions`, `password_reset_tokens`
- Existing business tables include: `employees`, `departments`, `tasks`, `task_assignments`, `settings`, `notifications`
- The supplied production migration history already includes all legacy migrations through `2026_07_31_033454_add_manager_advice_to_task_assignments_table`.
- The payroll migrations are intentionally newer and are the only application migrations expected to be pending on the supplied production dump.

## 1. Back up first

Before changing the live site:

1. In Hostinger hPanel, export the complete database `u303244667_taskdashboard` using phpMyAdmin/Database backups.
2. Download the SQL backup to a safe local folder.
3. Back up the current Laravel application files on Hostinger.
4. Keep the current production `.env` separately. Do not overwrite it with `.env.example`.

## 2. Hostinger database values

In Hostinger hPanel, open the database management page for the live database and record:

- Database host / hostname -> `DB_HOST`
- Database name -> `DB_DATABASE` (`u303244667_taskdashboard`)
- Database username -> `DB_USERNAME`
- Database password -> `DB_PASSWORD`
- Port -> normally `3306`

Do not assume `127.0.0.1` from a Windows PC points to Hostinger. `127.0.0.1` always means the machine on which PHP is currently running.

If the Laravel application is deployed on the same Hostinger account, use the Hostinger database hostname shown in hPanel. If connecting from a Windows PC, Hostinger must permit remote MySQL and the public client IP may need to be allowlisted. If remote MySQL is unavailable on the plan, deploy the app to Hostinger and connect server-to-database internally instead.

## 3. Production `.env`

Copy values from `.env.production.example` into the **existing production `.env`**, replacing only the relevant values. A typical database section is:

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
DB_CHARSET=utf8mb4
DB_COLLATION=utf8mb4_unicode_ci

SESSION_DRIVER=database
CACHE_STORE=database
QUEUE_CONNECTION=database
```

Never commit the real `.env` to Git and never place the database password in PHP, Blade, or JavaScript files.

## 4. Safe migration strategy

### Never run these on production

```bash
php artisan migrate:fresh
php artisan migrate:fresh --seed
php artisan db:wipe
php artisan migrate:reset
```

Do not run production seeders. Development seeders in this project now fail intentionally when `APP_ENV=production`.

### Check first

```bash
php artisan optimize:clear
php artisan migrate:status
```

On a database matching the supplied dump, the legacy migrations should already show as `Ran`. The new payroll migrations should show as `Pending`:

- `2026_09_30_000100_create_employee_salary_structures_table`
- `2026_09_30_000200_create_payrolls_table`
- `2026_09_30_000300_create_payroll_payments_table`
- `2026_09_30_000400_create_payroll_audit_logs_table`

The payroll migrations are additive and use `Schema::hasTable()` guards. The old `manager_advice` migration also uses a `Schema::hasColumn()` guard so it cannot add that production column a second time.

### Apply migrations

```bash
php artisan migrate --force
```

Do **not** add `--seed`.

## 5. Verify the database connection before migration

```bash
php artisan tinker
```

Then run:

```php
DB::connection()->getPdo();
config('database.connections.mysql.host');
config('database.connections.mysql.database');
```

The database should report `u303244667_taskdashboard`.

Verify existing users without exposing password hashes:

```php
\App\Models\User::count();
\App\Models\User::select('id', 'name', 'email', 'role_id')->get();
```

Verify employees and user relationships:

```php
\App\Models\Employee::count();
\App\Models\Employee::with('user')->get();
```

Exit Tinker:

```php
exit
```

## 6. Existing authentication

The application continues to authenticate against the existing `users.email` and `users.password` fields using Laravel authentication. Existing bcrypt password hashes are not replaced by the payroll deployment.

Do not run `UserSeeder` against production. Do not create replacement users just to make login work.

If a live user cannot sign in, verify in this order:

1. The application is connected to `u303244667_taskdashboard`.
2. The user exists in `users` with the expected email and role.
3. `Auth::attempt()` is using `email` and `password`.
4. `SESSION_DRIVER=database` and the existing `sessions` table is available.
5. Configuration cache was cleared after editing `.env`.
6. The role referenced by `users.role_id` still exists.

The supplied database uses `utf8mb4_unicode_ci`, which is case-insensitive for ordinary email comparisons.

## 7. Build and deploy application files

Where Hostinger provides SSH/terminal access:

```bash
composer install --no-dev --optimize-autoloader
npm install
npm run build
php artisan optimize:clear
php artisan migrate:status
php artisan migrate --force
php artisan storage:link
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

If Node/npm is unavailable on Hostinger, run `npm install` and `npm run build` locally, then upload the generated `public/build` directory with the application.

If `storage:link` already exists, Laravel may report that the link already exists; do not delete production storage just to recreate it.

## 8. Post-deployment verification

Test in this order:

1. Open the login page.
2. Sign in using an **existing live Hostinger user** and existing password.
3. Confirm Admin, Manager, and Employee role redirects still work.
4. Confirm departments, employees, tasks, assignments, settings, and notifications still load.
5. Admin: open Payroll Management and Salary Structures.
6. Add a salary structure to an existing employee.
7. Generate a payroll record.
8. Confirm duplicate employee/month/year payroll is blocked.
9. Employee: verify only their own payroll is visible.
10. Attempt another employee payroll ID as Employee; the server must return HTTP 403.
11. Manager: verify payroll administration is inaccessible.
12. Confirm payslip print/Save-as-PDF works.

## 9. Production rollback strategy

If the application code deployment fails but the payroll migrations succeeded:

1. Put the site in maintenance mode if required.
2. Restore the previous application files or previous Git revision.
3. Restore the previous `.env` if it was changed incorrectly.
4. Run `php artisan optimize:clear`.
5. Do **not** automatically run `migrate:rollback` after payroll has been used; that could delete payroll tables and payroll records.
6. Leave the additive payroll tables in place while restoring the old code, or restore the complete pre-deployment database backup if a full database rollback is truly required.

If the migration itself fails before completion, inspect the error and the current schema before retrying. Do not use `migrate:fresh` as a recovery method.

## 10. Important production rules

- Production migration command: `php artisan migrate --force`
- Never run `migrate:fresh` on the live database.
- Never seed production users.
- Never replace existing bcrypt hashes.
- Never change existing user/employee IDs for payroll integration.
- Payroll references the existing `employees.id` values.
- Keep `APP_DEBUG=false` in production.
- Keep a tested database backup before every schema deployment.
