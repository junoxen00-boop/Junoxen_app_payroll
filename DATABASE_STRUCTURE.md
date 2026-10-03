# Junoxen Database Structure - Hostinger Compatible

## Production database

The supplied Hostinger dump identifies the live database as `u303244667_taskdashboard` on MariaDB. Laravel uses the MySQL PDO driver, which is compatible with this schema.

The production database already contains all legacy tables and migration history. The payroll module adds four new tables without replacing existing authentication or employee data.

## Existing core relationships preserved

- `roles.id -> users.role_id`
- `users.id -> employees.user_id`
- `departments.id -> employees.department_id`
- `tasks.id -> task_assignments.task_id`
- `employees.id -> task_assignments.employee_id`
- `users.id -> notifications.user_id`

Payroll uses the existing employee primary keys.

## `employee_salary_structures`

| Field | Type | Null | Purpose |
|---|---|---:|---|
| id | BIGINT UNSIGNED | No | Primary key |
| employee_id | BIGINT UNSIGNED | No | FK -> employees.id |
| basic_salary | DECIMAL(12,2) | No | Basic salary |
| hra | DECIMAL(12,2) | No | HRA |
| conveyance_allowance | DECIMAL(12,2) | No | Allowance |
| medical_allowance | DECIMAL(12,2) | No | Allowance |
| special_allowance | DECIMAL(12,2) | No | Allowance |
| other_allowance | DECIMAL(12,2) | No | Allowance |
| overtime_rate | DECIMAL(12,2) | No | Per-hour overtime rate |
| pf_deduction | DECIMAL(12,2) | No | PF |
| esi_deduction | DECIMAL(12,2) | No | ESI |
| professional_tax | DECIMAL(12,2) | No | Professional tax |
| tds | DECIMAL(12,2) | No | TDS |
| loan_deduction | DECIMAL(12,2) | No | Loan deduction |
| other_deduction | DECIMAL(12,2) | No | Other recurring deduction |
| effective_from | DATE | No | Effective start |
| effective_to | DATE | Yes | Effective end |
| status | ENUM | No | Active / Inactive |
| notes | TEXT | Yes | Admin notes |
| created_by | BIGINT UNSIGNED | Yes | FK -> users.id |
| updated_by | BIGINT UNSIGNED | Yes | FK -> users.id |
| created_at / updated_at | TIMESTAMP | Yes | Laravel timestamps |

Indexes:

- `ess_emp_status_eff_idx` -> employee/status/effective_from
- `ess_effective_range_idx` -> effective_from/effective_to

## `payrolls`

`payroll_number` is unique. `employee_id` references the existing `employees.id`. `employee_id + payroll_month + payroll_year` is uniquely constrained by `payroll_emp_month_year_uq` to prevent duplicate monthly payroll.

All earnings and deduction money columns use `DECIMAL(12,2)`; overtime hours use `DECIMAL(8,2)`.

Statuses:

- Draft
- Generated
- Paid
- Cancelled

Index `payroll_period_status_idx` supports period/status reporting.

## `payroll_payments`

| Field | Type | Null | Purpose |
|---|---|---:|---|
| id | BIGINT UNSIGNED | No | Primary key |
| payroll_id | BIGINT UNSIGNED | No | FK -> payrolls.id |
| amount | DECIMAL(12,2) | No | Recorded amount |
| payment_date | DATE | No | Payment date |
| payment_method | VARCHAR(50) | No | Payment method |
| transaction_reference | VARCHAR(120) | Yes | Reference |
| notes | TEXT | Yes | Notes |
| recorded_by | BIGINT UNSIGNED | Yes | FK -> users.id |
| created_at / updated_at | TIMESTAMP | Yes | Laravel timestamps |

Index: `payroll_payment_date_idx`.

## `payroll_audit_logs`

| Field | Type | Null | Purpose |
|---|---|---:|---|
| id | BIGINT UNSIGNED | No | Primary key |
| payroll_id | BIGINT UNSIGNED | Yes | FK -> payrolls.id |
| user_id | BIGINT UNSIGNED | Yes | FK -> users.id |
| action | VARCHAR(80) | No | Audit action |
| old_values | JSON | Yes | Previous data |
| new_values | JSON | Yes | New data |
| ip_address | VARCHAR(45) | Yes | IPv4/IPv6 |
| created_at | TIMESTAMP | No | Audit timestamp |

Index: `payroll_audit_action_idx`.

## Production migration notes

The supplied live migration history already contains the legacy migrations through the `manager_advice` migration. The new payroll migration files are designed to be the additive pending migrations.

Use:

```bash
php artisan migrate:status
php artisan migrate --force
```

Never use `migrate:fresh` against production.

## Payroll statutory fields added 2026-10-03

### employee_salary_structures
- `pf_applicable` BOOLEAN NOT NULL DEFAULT 1 — whether employee PF calculation applies for this salary structure.
- `pf_wage_basis` DECIMAL(12,2) NULL — PF wage basis override; when null, payroll falls back to Basic Salary.

Legacy `pf_deduction` and `professional_tax` columns are retained for historical compatibility but are not authoritative for newly generated payroll.

### payrolls
- `leave_days` DECIMAL(5,2) NOT NULL DEFAULT 0 — total leave days recorded for the payroll period.
- `lop_deduction` DECIMAL(12,2) NOT NULL DEFAULT 0 — server-calculated monetary Loss of Pay deduction.
- `paid_days` DECIMAL(5,2) NOT NULL DEFAULT 0 — payroll-period days minus LOP days under the configured calendar-day policy.

Existing `leave_deduction` is retained for historical compatibility. New calculations set it to zero to avoid double deduction when LOP deduction is used.
