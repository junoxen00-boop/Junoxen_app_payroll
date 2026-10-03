# Junoxen Payroll Implementation Notes

## Local update commands
1. Back up the existing MySQL database.
2. Configure the local `.env` with the existing local database credentials.
3. Run:

```powershell
composer install
npm.cmd install
php artisan optimize:clear
php artisan migrate
npm.cmd run dev
php artisan serve
```

Use `php artisan migrate` only. Do not run `migrate:fresh` against existing payroll data.

## PF configuration requiring company confirmation
The implementation defaults the employee PF contribution rate to 12%. EPFO official guidance also identifies statutory categories where 10% can apply. If Junoxen belongs to a 10% category, set:

```env
PAYROLL_PF_EMPLOYEE_RATE_PERCENT=10
```

The application uses `pf_wage_basis` from the salary structure when supplied; otherwise it falls back to Basic Salary because the existing schema has no DA/retaining-allowance fields. Confirm the PF wage basis with Junoxen payroll/accounts before production use.

## LOP policy
The existing application used calendar-day proration, so this implementation retains:

`LOP Deduction = Basic Salary / Calendar Days in Month x LOP Days`

Paid leave is represented by Leave Days that are not included in LOP Days and therefore does not reduce salary.
