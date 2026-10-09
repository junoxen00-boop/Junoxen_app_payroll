<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\ImportAttendanceRequest;
use App\Models\AttendanceDailyRecord;
use App\Models\AttendanceImport;
use App\Models\Employee;
use App\Services\AttendanceImportService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AttendanceImportController extends Controller
{
    public function __construct(
        private AttendanceImportService $attendanceService
    ) {
    }

    public function index(): View
    {
        return view('admin.attendance.index', [
            'pageTitle' => 'Attendance Import',
            'imports' => AttendanceImport::query()
                ->with(['uploadedBy', 'confirmedBy'])
                ->latest('id')
                ->paginate(15),
        ]);
    }

    public function store(ImportAttendanceRequest $request): RedirectResponse
    {
        $import = $this->attendanceService->createPreview(
            $request->file('attendance_file'),
            $request->user()
        );

        return redirect()
            ->route('admin.payroll-management.attendance.show', $import)
            ->with('success', 'Attendance file parsed successfully. Review the records before confirming the import.');
    }

    public function show(AttendanceImport $attendanceImport, Request $request): View
    {
        $records = AttendanceDailyRecord::query()
            ->with(['employee.department', 'modifiedBy'])
            ->where('attendance_import_id', $attendanceImport->id)
            ->when($request->filled('review_status'), fn ($q) => $q->where('review_status', $request->input('review_status')))
            ->when($request->filled('employee_id'), fn ($q) => $q->where('employee_id', $request->integer('employee_id')))
            ->orderBy('source_employee_name')
            ->orderBy('attendance_date')
            ->paginate(40)
            ->withQueryString();

        $employeeSummaries = AttendanceDailyRecord::query()
            ->where('attendance_import_id', $attendanceImport->id)
            ->selectRaw('source_employee_id, source_employee_name, employee_id, match_method, COUNT(*) as days_imported, SUM(CASE WHEN attendance_status = ? THEN 1 ELSE 0 END) as present_days, SUM(late_minutes) as late_minutes, SUM(deductible_minutes) as deductible_minutes, SUM(CASE WHEN review_status = ? THEN 1 ELSE 0 END) as needs_review', ['Present', 'Needs Review'])
            ->groupBy('source_employee_id', 'source_employee_name', 'employee_id', 'match_method')
            ->orderBy('source_employee_name')
            ->get();

        return view('admin.attendance.show', [
            'pageTitle' => 'Attendance Import Review',
            'import' => $attendanceImport,
            'records' => $records,
            'employeeSummaries' => $employeeSummaries,
            'employees' => Employee::orderBy('full_name')->get(),
        ]);
    }

    public function confirm(AttendanceImport $attendanceImport, Request $request): RedirectResponse
    {
        $this->attendanceService->confirm($attendanceImport, $request->user());

        return back()->with('success', 'Attendance import confirmed. Payroll can now use Ready attendance records for this period.');
    }

    public function updateRecord(
        AttendanceDailyRecord $attendanceDailyRecord,
        Request $request
    ): RedirectResponse {
        $data = $request->validate([
            'employee_id' => ['nullable', 'integer', 'exists:employees,id'],
            'first_entry' => ['nullable', 'date_format:H:i'],
            'last_exit' => ['nullable', 'date_format:H:i'],
            'attendance_status' => ['nullable', 'in:Present,Paid Leave,Unpaid Leave,Weekly Off,Holiday,Absent,Attendance Missing'],
            'override_reason' => ['required', 'string', 'max:1000'],
        ]);

        $this->attendanceService->updateRecord(
            $attendanceDailyRecord,
            $data,
            $request->user()
        );

        return back()->with('success', 'Attendance record reviewed and updated.');
    }

    public function summary(Request $request): JsonResponse
    {
        $data = $request->validate([
            'employee_id' => ['required', 'integer', 'exists:employees,id'],
            'payroll_month' => ['required', 'integer', 'between:1,12'],
            'payroll_year' => ['required', 'integer', 'between:2000,2100'],
        ]);

        $employee = Employee::findOrFail((int) $data['employee_id']);
        $summary = $this->attendanceService->payrollSummary(
            $employee,
            (int) $data['payroll_month'],
            (int) $data['payroll_year']
        );

        return response()->json($summary);
    }
}