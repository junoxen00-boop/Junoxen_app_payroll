# Junoxen Payroll Changes

## Scope
Updated the existing Laravel payroll module only. Existing authentication, roles, employees, departments, payroll history, payment workflow and audit logging were preserved.

## Files added
- `app/Services/PayrollStatutoryService.php`
- `config/payroll.php`
- `database/migrations/2026_10_03_120000_add_statutory_payroll_fields.php`

## Main files modified
- `app/Services/PayrollService.php`
- `app/Http/Controllers/Admin/PayrollController.php`
- `app/Http/Requests/StorePayrollRequest.php`
- `app/Http/Requests/UpdatePayrollRequest.php`
- `app/Http/Requests/StoreSalaryStructureRequest.php`
- `app/Models/Payroll.php`
- `app/Models/EmployeeSalaryStructure.php`
- `resources/views/admin/payroll/create.blade.php`
- `resources/views/admin/payroll/_adjustments.blade.php`
- `resources/views/admin/payroll/bulk.blade.php`
- `resources/views/admin/payroll/show.blade.php`
- `resources/views/admin/salary-structures/_form.blade.php`
- `resources/views/payroll/_payslip-card.blade.php`
- `database/seeders/EmployeeSalaryStructureSeeder.php`
- `tests/Feature/Payroll/PayrollGenerationTest.php`
- `tests/Feature/Payroll/SalaryStructureTest.php`
- `.env.example`
- `.env.production.example`

## Telangana Professional Tax
Verified against the Telangana Commercial Taxes Department First Schedule on 2026-10-03:
- Up to Rs 15,000/month: Nil
- Rs 15,001 to Rs 20,000/month: Rs 150/month
- Above Rs 20,000/month: Rs 200/month

Implementation uses monthly Gross Earnings before deductions as the salary/wage slab basis and stores the calculated amount in `professional_tax`.

Official source: https://www.tgct.gov.in/tgportal/AllActs/APPT/APPTSchedule.aspx

## Provident Fund
Employee PF is calculated server-side and is no longer trusted from browser input.

Configuration is centralized in `config/payroll.php`.
- Default employee rate: 12% (configurable with `PAYROLL_PF_EMPLOYEE_RATE_PERCENT`).
- Historical wage ceiling before 17 Sep 2026: Rs 15,000.
- Wage ceiling from 17 Sep 2026: Rs 25,000.
- Contribution is rounded to the nearest rupee.

The Rs 25,000 ceiling was announced effective 17 Sep 2026 and notified by Gazette notification S.O. 5109(E). EPFO official material identifies the employee contribution as 12% for the standard rate and notes specified categories where 10% can apply. Junoxen payroll/accounting should confirm whether its establishment is subject to 12% or a statutory 10% category.

Because this project has no DA/retaining-allowance fields, PF wage basis uses `pf_wage_basis` when set on the salary structure and otherwise falls back to Basic Salary. This must be confirmed against Junoxen's actual PF wage components.

Government references:
- https://www.pib.gov.in/PressReleasePage.aspx?PRID=2310973
- https://www.epfindia.gov.in/site_docs/PDFs/MiscPDFs/ContributionRate.pdf

## Leave / LOP
- `Leave Days` records total leave days.
- `LOP Days` records the unpaid part of leave.
- Paid leave does not reduce salary.
- LOP Days cannot exceed Leave Days or the days in the selected month.
- Existing Junoxen calendar-day proration was retained as the documented company-policy assumption:
  `LOP Deduction = Basic Salary / Calendar Days In Month * LOP Days`.
- `leave_deduction` remains for legacy compatibility but new payroll calculation writes `0.00` to prevent double deduction.

## Payroll totals
- Gross Earnings = existing fixed earnings + Bonus.
- Total Deductions = Professional Tax + Provident Fund + LOP Deduction.
- Total Net Payable = Gross Earnings - Total Deductions.
- Paid Days = calendar days in payroll month - LOP Days.

Authoritative calculations are performed server-side using integer paise arithmetic for payroll money calculations.

## UI / Payslip
Generate Payroll now shows:
- Bonus
- Leave Days
- LOP Days
- Professional Tax (calculated)
- Provident Fund (calculated)
- LOP Deduction (calculated)
- Paid Days
- Total Deductions
- Total Net Payable

The payslip shows a simplified professional Employee Summary, Pay Summary, Earnings, statutory Deductions, Total Net Payable and amount in words.

## Database migration
Run only:

```bash
php artisan migrate
```

Do not use `migrate:fresh` on an existing database.

Added fields:
- `employee_salary_structures.pf_applicable`
- `employee_salary_structures.pf_wage_basis`
- `payrolls.leave_days`
- `payrolls.lop_deduction`
- `payrolls.paid_days`

## Verification performed in build environment
- PHP syntax checks passed for all modified PHP files.
- Laravel `route:list --path=payroll` succeeded and returned the expected payroll routes.
- Standalone server-side statutory checks returned PT values 0/150/200 and PF values Rs 1,800 before the Sep-2026 ceiling change and Rs 3,000 after it for a Rs 30,000 PF wage basis at the configured 12% rate.
- Standalone payroll calculation check for Sep 2026 with Basic Rs 30,000, fixed allowances Rs 18,500, Bonus Rs 1,000 and 2 LOP days produced Gross Rs 49,500, PF Rs 3,000, PT Rs 200, LOP Rs 2,000, Total Deductions Rs 5,200 and Net Payable Rs 44,300.

Full automated Pest/PHPUnit execution could not run in the build environment because the container PHP installation lacks the DOM extension and PDO SQLite driver. The test files were updated for execution on a normal Laravel/PHP environment with those extensions enabled.
