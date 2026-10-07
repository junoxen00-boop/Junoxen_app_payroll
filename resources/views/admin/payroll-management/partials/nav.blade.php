<div class="card mb-4">
    <div class="card-body p-0">
        <div class="list-group list-group-flush payroll-hub-nav">
            <a href="{{ route('admin.payroll-management.index') }}" class="list-group-item list-group-item-action py-3 {{ request()->routeIs('admin.payroll-management.index') ? 'active' : '' }}">
                <i class="bi bi-grid-1x2-fill me-2"></i>Payroll Overview
            </a>

            <div class="px-3 pt-3 pb-1 small text-uppercase text-muted fw-bold">Employee Management</div>
            <a href="{{ route('admin.payroll-management.employees.index') }}" class="list-group-item list-group-item-action {{ request()->routeIs('admin.payroll-management.employees.*') ? 'active' : '' }}">
                <i class="bi bi-people me-2"></i>Employees
            </a>
            <a href="{{ route('admin.payroll-management.leave.index') }}" class="list-group-item list-group-item-action {{ request()->routeIs('admin.payroll-management.leave.*') ? 'active' : '' }}">
                <i class="bi bi-calendar2-check me-2"></i>Leave
            </a>
            <a href="{{ route('admin.payroll-management.timesheets.index') }}" class="list-group-item list-group-item-action {{ request()->routeIs('admin.payroll-management.timesheets.*') ? 'active' : '' }}">
                <i class="bi bi-clock-history me-2"></i>Timesheets
            </a>

            <div class="px-3 pt-3 pb-1 small text-uppercase text-muted fw-bold">Payroll Processing</div>
            <a href="{{ route('admin.payroll.create') }}" class="list-group-item list-group-item-action {{ request()->routeIs('admin.payroll.create') ? 'active' : '' }}">
                <i class="bi bi-cash-coin me-2"></i>Pay Employees
            </a>
            <a href="{{ route('admin.payroll-management.superannuation.index') }}" class="list-group-item list-group-item-action {{ request()->routeIs('admin.payroll-management.superannuation.*') ? 'active' : '' }}">
                <i class="bi bi-piggy-bank me-2"></i>Superannuation
            </a>
            <a href="{{ route('admin.payroll-management.stp.index') }}" class="list-group-item list-group-item-action {{ request()->routeIs('admin.payroll-management.stp.*') ? 'active' : '' }}">
                <i class="bi bi-file-earmark-check me-2"></i>Single Touch Payroll
            </a>

            <div class="px-3 pt-3 pb-1 small text-uppercase text-muted fw-bold">Administration</div>
            <a href="{{ route('admin.payroll.index') }}" class="list-group-item list-group-item-action {{ request()->routeIs('admin.payroll.index') || request()->routeIs('admin.payroll.show') || request()->routeIs('admin.payroll.edit') ? 'active' : '' }}">
                <i class="bi bi-clock-history me-2"></i>Payroll History
            </a>
            <a href="{{ route('admin.payroll.reports') }}" class="list-group-item list-group-item-action {{ request()->routeIs('admin.payroll.reports') ? 'active' : '' }}">
                <i class="bi bi-bar-chart-line me-2"></i>Payroll Reports
            </a>

            <a href="{{ route('admin.payroll-management.settings.index') }}" class="list-group-item list-group-item-action py-3 mt-2 border-top {{ request()->routeIs('admin.payroll-management.settings.*') ? 'active' : '' }}">
                <i class="bi bi-gear me-2"></i>Payroll Settings
            </a>
        </div>
    </div>
</div>
