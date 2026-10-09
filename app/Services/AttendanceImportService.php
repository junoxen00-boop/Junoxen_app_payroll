<?php

namespace App\Services;

use App\Models\AttendanceDailyRecord;
use App\Models\AttendanceImport;
use App\Models\Employee;
use App\Models\EmployeeLeave;
use App\Models\PayrollManagementSetting;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class AttendanceImportService
{
    public function createPreview(
        UploadedFile $file,
        User $actor
    ): AttendanceImport {
        $spreadsheet = IOFactory::load(
            $file->getRealPath()
        );

        $sheet = $spreadsheet->getSheetByName(
            'Attendance Record'
        );

        if (! $sheet instanceof Worksheet) {
            throw ValidationException::withMessages([
                'attendance_file' =>
                    'The workbook must contain a sheet named "Attendance Record".',
            ]);
        }

        [$month, $year] =
            $this->extractPeriod($sheet);

        [$headerRow, $dayColumns] =
            $this->findHeader(
                $sheet,
                $month,
                $year
            );

        $parsedRows =
            $this->parseEmployeeRows(
                $sheet,
                $headerRow,
                $dayColumns,
                $month,
                $year
            );

        if ($parsedRows->isEmpty()) {
            throw ValidationException::withMessages([
                'attendance_file' =>
                    'No employee attendance rows were found in the uploaded workbook.',
            ]);
        }

        return DB::transaction(
            function () use (
                $file,
                $actor,
                $parsedRows,
                $month,
                $year
            ) {

                $import = AttendanceImport::create([
                    'original_filename' =>
                        $file->getClientOriginalName(),

                    'payroll_month' => $month,

                    'payroll_year' => $year,

                    'status' => 'Preview',

                    'uploaded_by' => $actor->id,
                ]);


                $employeeRows = 0;

                $matchedEmployees = [];

                $unmatchedEmployees = [];

                $needsReview = 0;


                foreach ($parsedRows as $row) {

                    $employeeRows++;


                    [
                        $employee,
                        $matchMethod,
                        $nameFallback
                    ] = $this->matchEmployee(
                        (string) $row[
                            'source_employee_id'
                        ],

                        (string) $row[
                            'source_employee_name'
                        ]
                    );


                    if ($employee) {

                        $matchedEmployees[
                            $row[
                                'source_employee_id'
                            ]
                        ] = true;

                    } else {

                        $unmatchedEmployees[
                            $row[
                                'source_employee_id'
                            ]
                        ] = true;
                    }


                    foreach (
                        $row['days']
                        as $day
                    ) {

                        $analysis =
                            $this->analyseDay(
                                $employee,

                                $day['date'],

                                $day['raw'],

                                $nameFallback
                            );


                        if (
                            $analysis[
                                'review_status'
                            ] === 'Needs Review'
                        ) {
                            $needsReview++;
                        }


                        AttendanceDailyRecord::create(
                            array_merge(
                                $analysis,
                                [
                                    'attendance_import_id' =>
                                        $import->id,

                                    'employee_id' =>
                                        $employee?->id,

                                    'source_employee_id' =>
                                        $row[
                                            'source_employee_id'
                                        ],

                                    'source_employee_name' =>
                                        $row[
                                            'source_employee_name'
                                        ],

                                    'source_department' =>
                                        $row[
                                            'source_department'
                                        ],

                                    'attendance_date' =>
                                        $day[
                                            'date'
                                        ]->toDateString(),

                                    'match_method' =>
                                        $matchMethod,
                                ]
                            )
                        );
                    }
                }


                $import->update([
                    'row_count' =>
                        $employeeRows,

                    'matched_count' =>
                        count(
                            $matchedEmployees
                        ),

                    'unmatched_count' =>
                        count(
                            $unmatchedEmployees
                        ),

                    'needs_review_count' =>
                        $needsReview,
                ]);


                return $import->fresh();
            }
        );
    }


    public function confirm(
        AttendanceImport $import,
        User $actor
    ): AttendanceImport {

        if (
            $import->status ===
            'Confirmed'
        ) {
            return $import;
        }


        return DB::transaction(
            function () use (
                $import,
                $actor
            ) {

                AttendanceImport::query()
                    ->where(
                        'payroll_month',
                        $import->payroll_month
                    )
                    ->where(
                        'payroll_year',
                        $import->payroll_year
                    )
                    ->where(
                        'status',
                        'Confirmed'
                    )
                    ->where(
                        'id',
                        '<>',
                        $import->id
                    )
                    ->update([
                        'status' =>
                            'Superseded',
                    ]);


                $import->update([
                    'status' =>
                        'Confirmed',

                    'confirmed_at' =>
                        now(),

                    'confirmed_by' =>
                        $actor->id,
                ]);


                return $import->fresh();
            }
        );
    }


    public function updateRecord(
        AttendanceDailyRecord $record,
        array $input,
        User $actor
    ): AttendanceDailyRecord {

        $employee =
            $record->employee;


        if (! $employee) {

            if (
                ! empty(
                    $input['employee_id']
                )
            ) {

                $employee =
                    Employee::findOrFail(
                        (int)
                        $input[
                            'employee_id'
                        ]
                    );

            } else {

                throw ValidationException::withMessages([
                    'employee_id' =>
                        'Select the employee before approving this record.',
                ]);
            }
        }


        $rawPunches =
            trim(
                (string)
                (
                    $record->raw_punches
                    ?? ''
                )
            );


        $analysis =
            $this->analyseDay(
                $employee,

                $record->attendance_date,

                $rawPunches,

                false,

                true
            );


        if (
            ! empty(
                $input[
                    'attendance_status'
                ]
            )
        ) {

            $analysis[
                'attendance_status'
            ] =
                $input[
                    'attendance_status'
                ];
        }


        if (
            ! empty(
                $input[
                    'first_entry'
                ]
            )
        ) {

            $analysis[
                'first_entry'
            ] =
                $input[
                    'first_entry'
                ];


            $analysis[
                'late_minutes'
            ] =
                $this->lateMinutesForTime(
                    $input[
                        'first_entry'
                    ]
                );


            $analysis[
                'deductible_minutes'
            ] =
                $this->deductibleLateMinutes(
                    $analysis[
                        'late_minutes'
                    ]
                );
        }


        if (
            ! empty(
                $input[
                    'last_exit'
                ]
            )
        ) {

            $analysis[
                'last_exit'
            ] =
                $input[
                    'last_exit'
                ];
        }


        $record->update(
            array_merge(
                $analysis,
                [
                    'employee_id' =>
                        $employee->id,

                    'match_method' =>
                        $record->match_method
                            ?: 'admin_override',

                    'review_status' =>
                        'Ready',

                    'review_note' =>
                        'Reviewed by Admin.',

                    'override_reason' =>
                        $input[
                            'override_reason'
                        ],

                    'modified_by' =>
                        $actor->id,

                    'modified_at' =>
                        now(),
                ]
            )
        );


        $this->refreshImportCounts(
            $record->import
        );


        return $record->fresh();
    }


    public function payrollSummary(
        Employee $employee,
        int $month,
        int $year
    ): array {

        $settings =
            $this->settings();


        if (
            ! $settings
                ->attendance_payroll_enabled
        ) {

            return [
                'enabled' => false,

                'imported' => false,

                'ready' => true,

                'import_id' => null,

                'days_imported' => 0,

                'present_days' => 0,

                'late_minutes' => 0,

                'deductible_minutes' => 0,

                'needs_review_count' => 0,
            ];
        }


        $import =
            AttendanceImport::query()
                ->where(
                    'payroll_month',
                    $month
                )
                ->where(
                    'payroll_year',
                    $year
                )
                ->where(
                    'status',
                    'Confirmed'
                )
                ->latest('id')
                ->first();


        if (! $import) {

            return [
                'enabled' => true,

                'imported' => false,

                'ready' => false,

                'import_id' => null,

                'days_imported' => 0,

                'present_days' => 0,

                'late_minutes' => 0,

                'deductible_minutes' => 0,

                'needs_review_count' => 0,
            ];
        }


        $records =
            AttendanceDailyRecord::query()
                ->where(
                    'attendance_import_id',
                    $import->id
                )
                ->where(
                    'employee_id',
                    $employee->id
                )
                ->get();


        $needsReview =
            $records
                ->where(
                    'review_status',
                    'Needs Review'
                )
                ->count();


        return [
            'enabled' => true,

            'imported' =>
                $records->isNotEmpty(),

            'ready' =>
                $records->isNotEmpty()
                && $needsReview === 0,

            'import_id' =>
                $import->id,

            'days_imported' =>
                $records->count(),

            'present_days' =>
                $records
                    ->whereIn(
                        'attendance_status',
                        [
                            'Present',
                            'Paid Leave',
                        ]
                    )
                    ->count(),

            'late_minutes' =>
                (int)
                $records
                    ->where(
                        'review_status',
                        'Ready'
                    )
                    ->sum(
                        'late_minutes'
                    ),

            'deductible_minutes' =>
                (int)
                $records
                    ->where(
                        'review_status',
                        'Ready'
                    )
                    ->sum(
                        'deductible_minutes'
                    ),

            'needs_review_count' =>
                $needsReview,
        ];
    }


    public function settings():
        PayrollManagementSetting
    {

        return
            PayrollManagementSetting
                ::firstOrCreate(
                    [],
                    [
                        'company_name' =>
                            'Junoxen PVT LTD',

                        'pay_frequency' =>
                            'Monthly',

                        'currency' =>
                            'INR',

                        'default_payment_method' =>
                            'Bank Transfer',

                        'lop_calculation_basis' =>
                            'Calendar Days',

                        'payroll_year' =>
                            now()->year,

                        'attendance_payroll_enabled' =>
                            false,

                        'grace_minutes' =>
                            10,

                        'grace_deduction_mode' =>
                            'excess',

                        'standard_work_minutes_per_day' =>
                            480,

                        'deduct_late_arrival' =>
                            true,

                        'late_deduction_rounding' =>
                            'Exact Minutes',
                    ]
                );
    }


    private function extractPeriod(
        Worksheet $sheet
    ): array {

        $madeDate = null;


        for (
            $row = 1;
            $row <= min(
                10,
                $sheet->getHighestRow()
            );
            $row++
        ) {

            $value =
                trim(
                    (string)
                    $sheet
                        ->getCell(
                            "A{$row}"
                        )
                        ->getValue()
                );


            if (
                preg_match(
                    '/Made Date:\s*(\d{4})\/(\d{2})\/(\d{2})\s*-\s*(\d{4})\/(\d{2})\/(\d{2})/i',
                    $value,
                    $m
                )
            ) {

                $madeDate =
                    Carbon::create(
                        (int) $m[1],
                        (int) $m[2],
                        (int) $m[3]
                    );

                break;
            }
        }


        if (! $madeDate) {

            throw ValidationException::withMessages([
                'attendance_file' =>
                    'The workbook is missing a valid Made Date period.',
            ]);
        }


        return [
            $madeDate->month,
            $madeDate->year,
        ];
    }


    private function findHeader(
        Worksheet $sheet,
        int $month,
        int $year
    ): array {

        for (
            $row = 1;
            $row <= min(
                20,
                $sheet->getHighestRow()
            );
            $row++
        ) {

            $a =
                trim(
                    (string)
                    $sheet
                        ->getCell(
                            "A{$row}"
                        )
                        ->getValue()
                );

            $b =
                trim(
                    (string)
                    $sheet
                        ->getCell(
                            "B{$row}"
                        )
                        ->getValue()
                );

            $c =
                trim(
                    (string)
                    $sheet
                        ->getCell(
                            "C{$row}"
                        )
                        ->getValue()
                );


            if (
                strcasecmp(
                    $a,
                    'Employee ID'
                ) === 0
                &&
                strcasecmp(
                    $b,
                    'Name'
                ) === 0
                &&
                strcasecmp(
                    $c,
                    'Department'
                ) === 0
            ) {

                $dayColumns = [];


                $highestColumn =
                    Coordinate
                        ::columnIndexFromString(
                            $sheet
                                ->getHighestColumn()
                        );


                for (
                    $col = 4;
                    $col <= $highestColumn;
                    $col++
                ) {

                    $value =
                        $sheet
                            ->getCell(
                                [
                                    $col,
                                    $row,
                                ]
                            )
                            ->getValue();


                    if (
                        is_numeric($value)
                        &&
                        (int) $value >= 1
                        &&
                        (int) $value <= 31
                    ) {

                        $day =
                            (int) $value;


                        if (
                            checkdate(
                                $month,
                                $day,
                                $year
                            )
                        ) {

                            $dayColumns[
                                $col
                            ] = $day;
                        }
                    }
                }


                if (
                    $dayColumns === []
                ) {
                    break;
                }


                return [
                    $row,
                    $dayColumns,
                ];
            }
        }


        throw ValidationException::withMessages([
            'attendance_file' =>
                'Could not find the expected Employee ID / Name / Department attendance header.',
        ]);
    }


    private function parseEmployeeRows(
        Worksheet $sheet,
        int $headerRow,
        array $dayColumns,
        int $month,
        int $year
    ): Collection {

        $rows = collect();


        for (
            $row =
                $headerRow + 1;

            $row <=
                $sheet->getHighestRow();

            $row++
        ) {

            $sourceId =
                trim(
                    (string)
                    $sheet
                        ->getCell(
                            [
                                1,
                                $row,
                            ]
                        )
                        ->getFormattedValue()
                );


            $name =
                trim(
                    (string)
                    $sheet
                        ->getCell(
                            [
                                2,
                                $row,
                            ]
                        )
                        ->getFormattedValue()
                );


            $department =
                trim(
                    (string)
                    $sheet
                        ->getCell(
                            [
                                3,
                                $row,
                            ]
                        )
                        ->getFormattedValue()
                );


            if (
                $sourceId === ''
                &&
                $name === ''
            ) {
                continue;
            }


            $days = [];


            foreach (
                $dayColumns
                as $col => $day
            ) {

                $days[] = [
                    'date' =>
                        Carbon::create(
                            $year,
                            $month,
                            $day
                        ),

                    'raw' =>
                        (string)
                        (
                            $sheet
                                ->getCell(
                                    [
                                        $col,
                                        $row,
                                    ]
                                )
                                ->getFormattedValue()
                            ?? ''
                        ),
                ];
            }


            $rows->push([
                'source_employee_id' =>
                    $sourceId,

                'source_employee_name' =>
                    $name,

                'source_department' =>
                    $department,

                'days' =>
                    $days,
            ]);
        }


        return $rows;
    }


    private function matchEmployee(
        string $sourceId,
        string $sourceName
    ): array {

        $employee =
            Employee::query()
                ->where(
                    'attendance_employee_id',
                    $sourceId
                )
                ->first();


        if ($employee) {

            return [
                $employee,
                'attendance_employee_id',
                false,
            ];
        }


        $normalizedSource =
            $this->normalizeEmployeeId(
                $sourceId
            );


        $employee =
            Employee::query()
                ->get()
                ->first(
                    fn (
                        Employee $item
                    ) =>
                        $this
                            ->normalizeEmployeeId(
                                (string)
                                $item
                                    ->employee_id
                            )
                        ===
                        $normalizedSource
                );


        if (
            $employee
            &&
            $normalizedSource !== ''
        ) {

            return [
                $employee,
                'normalized_employee_id',
                false,
            ];
        }


        $normalizedName =
            $this->normalizeName(
                $sourceName
            );


        $employee =
            Employee::query()
                ->get()
                ->first(
                    fn (
                        Employee $item
                    ) =>
                        $this
                            ->normalizeName(
                                (string)
                                $item
                                    ->full_name
                            )
                        ===
                        $normalizedName
                );


        if (
            $employee
            &&
            $normalizedName !== ''
        ) {

            return [
                $employee,
                'name_fallback',
                true,
            ];
        }


        return [
            null,
            'unmatched',
            true,
        ];
    }


    private function analyseDay(
        ?Employee $employee,
        Carbon|string $date,
        string $raw,
        bool $forceReview = false,
        bool $adminOverride = false
    ): array {

        $date =
            $date instanceof Carbon
                ? $date
                : Carbon::parse($date);


        $settings =
            $this->settings();


        $punches =
            $this->parsePunches(
                $raw
            );


        $uniquePunches =
            array_values(
                array_unique(
                    $punches
                )
            );


        $hasDuplicates =
            count($uniquePunches)
            !==
            count($punches);


        $punches =
            $uniquePunches;


        $leave =
            $employee
                ? $this
                    ->approvedLeaveForDate(
                        $employee,
                        $date
                    )
                : null;


        if ($leave) {

            return [
                'raw_punches' =>
                    $raw !== ''
                        ? trim($raw)
                        : null,

                'first_entry' => null,

                'last_exit' => null,

                'worked_minutes' => 0,

                'scheduled_minutes' =>
                    (int)
                    $settings
                        ->standard_work_minutes_per_day,

                'late_minutes' => 0,

                'deductible_minutes' => 0,

                'attendance_status' =>
                    $leave->is_paid
                        ? 'Paid Leave'
                        : 'Unpaid Leave',

                'review_status' =>
                    $forceReview
                        ? 'Needs Review'
                        : 'Ready',

                'review_note' =>
                    $forceReview
                        ? 'Employee match requires review.'
                        : null,
            ];
        }


        if ($employee === null) {

            return [
                'raw_punches' =>
                    $raw !== ''
                        ? trim($raw)
                        : null,

                'first_entry' =>
                    $punches[0]
                        ?? null,

                'last_exit' =>
                    count($punches) >= 2
                        ? end($punches)
                        : null,

                'worked_minutes' =>
                    0,

                'scheduled_minutes' =>
                    (int)
                    $settings
                        ->standard_work_minutes_per_day,

                'late_minutes' =>
                    0,

                'deductible_minutes' =>
                    0,

                'attendance_status' =>
                    $punches
                        ? 'Present'
                        : 'Attendance Missing',

                'review_status' =>
                    'Needs Review',

                'review_note' =>
                    'Employee could not be matched.',
            ];
        }


        if ($punches === []) {

            return [
                'raw_punches' =>
                    null,

                'first_entry' =>
                    null,

                'last_exit' =>
                    null,

                'worked_minutes' =>
                    0,

                'scheduled_minutes' =>
                    (int)
                    $settings
                        ->standard_work_minutes_per_day,

                'late_minutes' =>
                    0,

                'deductible_minutes' =>
                    0,

                'attendance_status' =>
                    'Attendance Missing',

                'review_status' =>
                    $forceReview
                        ? 'Needs Review'
                        : 'Ready',

                'review_note' =>
                    $forceReview
                        ? 'Employee match requires review.'
                        : null,
            ];
        }


        $first =
            $punches[0];


        $last =
            count($punches) >= 2
                ? end($punches)
                : null;


        $workedMinutes =
            $this->workedMinutes(
                $punches
            );


        $lateMinutes =
            $this->lateMinutesForTime(
                $first
            );


        $deductible =
            $settings
                ->deduct_late_arrival
                ?
                    $this
                        ->deductibleLateMinutes(
                            $lateMinutes
                        )
                :
                    0;


        $reviewReasons = [];


        if (
            count($punches) === 1
        ) {

            $reviewReasons[] =
                'Only one punch was recorded.';

        } elseif (
            count($punches) % 2
            !== 0
        ) {

            $reviewReasons[] =
                'Odd number of punches.';
        }


        if ($hasDuplicates) {

            $reviewReasons[] =
                'Duplicate punch times were found.';
        }


        if ($forceReview) {

            $reviewReasons[] =
                'Employee match used name fallback and requires confirmation.';
        }


        return [
            'raw_punches' =>
                trim($raw),

            'first_entry' =>
                $first,

            'last_exit' =>
                $last,

            'worked_minutes' =>
                $workedMinutes,

            'scheduled_minutes' =>
                (int)
                $settings
                    ->standard_work_minutes_per_day,

            'late_minutes' =>
                $lateMinutes,

            'deductible_minutes' =>
                $deductible,

            'attendance_status' =>
                'Present',

            'review_status' =>
                $adminOverride
                ||
                $reviewReasons === []
                    ? 'Ready'
                    : 'Needs Review',

            'review_note' =>
                $reviewReasons
                    ? implode(
                        ' ',
                        $reviewReasons
                    )
                    : null,
        ];
    }


    private function parsePunches(
        string $raw
    ): array {

        $parts =
            preg_split(
                '/\R+/',
                trim($raw)
            )
            ?: [];


        $times = [];


        foreach (
            $parts
            as $part
        ) {

            $part =
                trim(
                    $part
                );


            if ($part === '') {
                continue;
            }


            if (
                ! preg_match(
                    '/^(?:[01]?\d|2[0-3]):[0-5]\d(?::[0-5]\d)?$/',
                    $part
                )
            ) {
                continue;
            }


            $pieces =
                explode(
                    ':',
                    $part
                );


            $hour =
                str_pad(
                    (string)
                    ((int) $pieces[0]),
                    2,
                    '0',
                    STR_PAD_LEFT
                );


            $minute =
                str_pad(
                    (string)
                    ((int) $pieces[1]),
                    2,
                    '0',
                    STR_PAD_LEFT
                );


            $second =
                isset(
                    $pieces[2]
                )
                    ?
                    str_pad(
                        (string)
                        ((int)
                        $pieces[2]),
                        2,
                        '0',
                        STR_PAD_LEFT
                    )
                    :
                    '00';


            $times[] =
                "{$hour}:{$minute}:{$second}";
        }


        sort($times);


        return $times;
    }


    private function workedMinutes(
        array $punches
    ): int {

        $minutes = 0;


        for (
            $i = 0;
            $i + 1 <
                count($punches);
            $i += 2
        ) {

            $start =
                Carbon::createFromFormat(
                    'H:i:s',
                    $punches[$i]
                );


            $end =
                Carbon::createFromFormat(
                    'H:i:s',
                    $punches[
                        $i + 1
                    ]
                );


            if (
                $end
                    ->greaterThan(
                        $start
                    )
            ) {

                $minutes +=
                    (int)
                    $start
                        ->diffInMinutes(
                            $end
                        );
            }
        }


        return $minutes;
    }


    public function lateMinutesForTime(
        ?string $firstEntry
    ): int {

        if (! $firstEntry) {
            return 0;
        }


        $settings =
            $this->settings();


        if (
            ! $settings
                ->default_shift_start
        ) {
            return 0;
        }


        $shiftStart =
            (string)
            $settings
                ->default_shift_start;


        if (
            strlen($shiftStart)
            === 5
        ) {
            $shiftStart .= ':00';
        }


        $entryValue =
            $firstEntry;


        if (
            strlen($entryValue)
            === 5
        ) {
            $entryValue .= ':00';
        }


        $start =
            Carbon::createFromFormat(
                'H:i:s',
                $shiftStart
            );


        $entry =
            Carbon::createFromFormat(
                'H:i:s',
                $entryValue
            );


        if (
            $entry
                ->lessThanOrEqualTo(
                    $start
                )
        ) {
            return 0;
        }


        return
            (int)
            $start
                ->diffInMinutes(
                    $entry
                );
    }


    public function deductibleLateMinutes(
        int $lateMinutes
    ): int {

        $settings =
            $this->settings();


        if (
            ! $settings
                ->deduct_late_arrival
            ||
            $lateMinutes <=
                (int)
                $settings
                    ->grace_minutes
        ) {
            return 0;
        }


        $minutes =
            $settings
                ->grace_deduction_mode
                === 'full'
                ?
                    $lateMinutes
                :
                    max(
                        0,
                        $lateMinutes
                        -
                        (int)
                        $settings
                            ->grace_minutes
                    );


        return $this->roundMinutes(
            $minutes,
            (string)
            $settings
                ->late_deduction_rounding
        );
    }


    private function roundMinutes(
        int $minutes,
        string $rounding
    ): int {

        $unit =
            match ($rounding) {

                'Nearest 15 Minutes'
                    => 15,

                'Nearest 30 Minutes'
                    => 30,

                'Whole Hour'
                    => 60,

                default
                    => 1,
            };


        if ($unit === 1) {
            return $minutes;
        }


        return
            (int)
            (
                round(
                    $minutes
                    /
                    $unit
                )
                *
                $unit
            );
    }


    private function approvedLeaveForDate(
        Employee $employee,
        Carbon $date
    ): ?EmployeeLeave {

        return
            EmployeeLeave::query()
                ->where(
                    'employee_id',
                    $employee->id
                )
                ->where(
                    'status',
                    'Approved'
                )
                ->whereDate(
                    'start_date',
                    '<=',
                    $date
                )
                ->whereDate(
                    'end_date',
                    '>=',
                    $date
                )
                ->first();
    }


    private function refreshImportCounts(
        AttendanceImport $import
    ): void {

        $import->update([
            'matched_count' =>
                AttendanceDailyRecord::query()
                    ->where(
                        'attendance_import_id',
                        $import->id
                    )
                    ->whereNotNull(
                        'employee_id'
                    )
                    ->distinct(
                        'source_employee_id'
                    )
                    ->count(
                        'source_employee_id'
                    ),

            'unmatched_count' =>
                AttendanceDailyRecord::query()
                    ->where(
                        'attendance_import_id',
                        $import->id
                    )
                    ->whereNull(
                        'employee_id'
                    )
                    ->distinct(
                        'source_employee_id'
                    )
                    ->count(
                        'source_employee_id'
                    ),

            'needs_review_count' =>
                AttendanceDailyRecord::query()
                    ->where(
                        'attendance_import_id',
                        $import->id
                    )
                    ->where(
                        'review_status',
                        'Needs Review'
                    )
                    ->count(),
        ]);
    }


    private function normalizeEmployeeId(
        string $value
    ): string {

        $digits =
            preg_replace(
                '/\D+/',
                '',
                $value
            )
            ?? '';


        return
            ltrim(
                $digits,
                '0'
            )
            ?:
            (
                $digits === '0'
                    ? '0'
                    : ''
            );
    }


    private function normalizeName(
        string $value
    ): string {

        return strtolower(
            preg_replace(
                '/\s+/',
                ' ',
                trim($value)
            )
            ?? ''
        );
    }
}