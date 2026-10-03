<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>
    {{ $pageTitle ?? 'Dashboard' }}
    @if($appSetting?->company_name)
        - {{ $appSetting->company_name }}
    @endif
</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

    <style>

:root{

    --primary:#2563eb;
    --primary-dark:#1d4ed8;
    --sidebar:#0f172a;
    --sidebar-hover:#1e293b;
    --background:#f8fafc;
    --card:#ffffff;
    --text:#0f172a;
    --muted:#64748b;
    --border:#e2e8f0;
    --success:#16a34a;
    --warning:#f59e0b;
    --danger:#ef4444;

}

*{
    transition:.25s;
}

body{

    background:var(--background);
    color:var(--text);
    font-family:
    Inter,
    "Segoe UI",
    sans-serif;

}

.admin-wrapper{

    display:flex;
    min-height:100vh;

}

.sidebar{

    width:270px;
    background:linear-gradient(
        180deg,
        #0f172a,
        #111827
    );

    color:white;

    position:fixed;

    left:0;
    top:0;
    bottom:0;

    overflow-y:auto;

    box-shadow:
    0 10px 30px rgba(0,0,0,.15);

}

.sidebar-brand{

    padding:28px;

    border-bottom:
    1px solid rgba(255,255,255,.08);

}

.sidebar-brand h5{

    font-weight:800;

    font-size:24px;

    margin:0;

}

.sidebar-menu{

    padding:18px;

}

.sidebar-menu a{

    display:flex;

    align-items:center;

    gap:14px;

    width:100%;

    text-decoration:none;

    color:#cbd5e1;

    padding:14px 18px;

    border-radius:14px;

    font-weight:600;

    margin-bottom:8px;

}

.sidebar-menu a:hover{

    background:var(--sidebar-hover);

    color:white;

    transform:translateX(6px);

}

.sidebar-menu a.active{

    background:linear-gradient(
    90deg,
    var(--primary),
    #4f46e5);

    color:white;

    box-shadow:
    0 8px 20px rgba(37,99,235,.30);

}

.sidebar-menu i{

    font-size:18px;

}

.admin-main{

    flex:1;

    margin-left:270px;

}

.topbar{

    height:82px;

    background:white;

    border-bottom:
    1px solid var(--border);

    display:flex;

    justify-content:space-between;

    align-items:center;

    padding:0 34px;

    position:sticky;

    top:0;

    z-index:100;

}

.topbar h5{

    font-weight:700;

}

.content{

    padding:34px;

}

.card{

    border:none;

    border-radius:20px;

    background:white;

    box-shadow:
    0 8px 24px rgba(15,23,42,.05);

}

.btn{

    border-radius:12px;

    font-weight:600;

    padding:.65rem 1.1rem;

}

.table{

    vertical-align:middle;

}

.table thead{

    background:#f8fafc;

}

.form-control,
.form-select{

    border-radius:12px;

    border:1px solid var(--border);

}

.form-control:focus,
.form-select:focus{

    border-color:var(--primary);

    box-shadow:
    0 0 0 .2rem rgba(37,99,235,.12);

}

.badge{

    border-radius:8px;

    padding:7px 10px;

}

.mobile-menu-btn {
    display: none;
}



@media(max-width:992px){

    html,
    body{
        width:100%;
        max-width:100%;
        overflow-x:hidden;
    }

    .admin-wrapper{
        width:100%;
        max-width:100%;
        overflow-x:hidden;
    }

    .sidebar{
        left:-270px;
        width:270px;
        max-width:270px;
    }

    .sidebar.mobile-open{
        left:0 !important;
        z-index:1050 !important;
    }

    .mobile-menu-btn{
        display:block;
        border:none;
        background:#f3f4f6;
        font-size:24px;
        width:44px;
        height:44px;
        min-width:44px;
        border-radius:10px;
        cursor:pointer;
        margin-right:10px;
    }

    .admin-header{
    width:100% !important;
    max-width:100% !important;
    box-sizing:border-box;
    overflow:hidden;
    display:flex;
    align-items:center;
}

.admin-header > *{
    min-width:0;
}

.admin-header h1,
.admin-header h2,
.admin-header h3{
    min-width:0;
    white-space:nowrap;
}

.admin-header .logout-btn{
    flex-shrink:0;
}

    .admin-main{
        margin-left:0 !important;
        width:100% !important;
        max-width:100% !important;
        min-width:0 !important;
        box-sizing:border-box;
        overflow-x:hidden;
    }

    .admin-main > *{
        max-width:100%;
        box-sizing:border-box;
    }

    .table-responsive{
        max-width:100%;
        overflow-x:auto;
        -webkit-overflow-scrolling:touch;
    }




    .topbar{
        width:100%;
        max-width:100%;
        padding:0 12px;
        box-sizing:border-box;
        overflow:hidden;
    }

    .topbar > div:first-of-type{
        min-width:0;
        flex:1;
    }

    .topbar > div:last-child{
        gap:6px !important;
        flex-shrink:0;
    }

    .topbar .btn-danger{
        padding:.55rem .7rem;
        white-space:nowrap;
    }
}

</style>

</head>

<body>

<div class="admin-wrapper">

   <aside class="sidebar">

    <div class="sidebar-brand text-center">

        <div class="mb-3">

            @if($appSetting?->company_logo)

    <img
        src="{{ asset('storage/'.$appSetting->company_logo) }}"
        alt="Company Logo"
        style="
            width:70px;
            height:70px;
            object-fit:contain;
            background:white;
            border-radius:18px;
            padding:8px;
            box-shadow:0 10px 25px rgba(37,99,235,.25);
        ">

@else

    <div style="
        width:70px;
        height:70px;
        border-radius:18px;
        background:linear-gradient(135deg,#2563eb,#4f46e5);
        display:flex;
        align-items:center;
        justify-content:center;
        margin:auto;
        font-size:30px;
        color:white;
        font-weight:bold;
        box-shadow:0 10px 25px rgba(37,99,235,.25);
    ">
        J
    </div>

@endif

        </div>

        <h5 class="mb-1">
    {{ $appSetting?->company_name ?: 'Junoxen PVT LTD' }}
</h5>

        <small class="text-white-50">
            Employee Management
        </small>

    </div>

    <div class="px-4 pt-3 pb-2">

        <div class="rounded-4 p-3"
             style="background:rgba(255,255,255,.06);">

            <div class="fw-bold">
                {{ auth()->user()->name }}
            </div>

            <small class="text-white-50">

                {{ auth()->user()->role->name }}

            </small>

        </div>

    </div>

    <nav class="sidebar-menu mt-2">

        {{-- Dashboard --}}
<a href="
@if(auth()->user()->role->name=='Admin')
{{ route('dashboard') }}
@elseif(auth()->user()->role->name=='Manager')
{{ route('manager.dashboard') }}
@else
{{ route('employee.dashboard') }}
@endif
"
class="
@if(auth()->user()->role->name=='Employee')
{{ request()->routeIs('employee.dashboard') ? 'active' : '' }}
@elseif(auth()->user()->role->name=='Manager')
{{ request()->routeIs('manager.dashboard') ? 'active' : '' }}
@else
{{ request()->routeIs('dashboard') ? 'active' : '' }}
@endif
">

    <i class="bi bi-speedometer2"></i>

    Dashboard

</a>

@if(auth()->user()->role->name=='Employee')

<a href="{{ route('employee.tasks.index') }}"
   class="{{ request()->routeIs('employee.tasks.*') ? 'active' : '' }}">

    <i class="bi bi-list-task"></i>

    My Tasks

</a>

<a href="{{ route('employee.payroll.index') }}"
   class="{{ request()->routeIs('employee.payroll.*') ? 'active' : '' }}">
    <i class="bi bi-receipt"></i>
    My Payroll
</a>

<a href="{{ route('profile.edit') }}"
   class="{{ request()->routeIs('profile.edit') ? 'active' : '' }}">

    <i class="bi bi-person-circle"></i>

    Profile

</a>

@endif

        @if(auth()->user()->role->name=='Admin')

        <a href="{{ route('departments.index') }}"
           class="{{ request()->routeIs('departments.*') ? 'active' : '' }}">

            <i class="bi bi-building"></i>

            Departments

        </a>

        <a href="{{ route('employees.index') }}"
           class="{{ request()->routeIs('employees.*') ? 'active' : '' }}">

            <i class="bi bi-people-fill"></i>

            Employees

        </a>

        <a href="{{ route('users.index') }}"
   class="{{ request()->routeIs('users.*') ? 'active' : '' }}">

    <i class="bi bi-people-fill"></i>

    Users

</a>

        <a href="{{ route('tasks.index') }}"
           class="{{ request()->routeIs('tasks.*') ? 'active' : '' }}">

            <i class="bi bi-list-check"></i>

            Tasks

        </a>

        <hr class="border-secondary">

        <div class="text-white-50 small text-uppercase px-3 pt-2 pb-1" style="letter-spacing:.08em;">Payroll Management</div>

        <a href="{{ route('admin.payroll.dashboard') }}"
           class="{{ request()->routeIs('admin.payroll.dashboard') ? 'active' : '' }}">
            <i class="bi bi-speedometer"></i>
            Payroll Dashboard
        </a>

        <a href="{{ route('admin.payroll.create') }}"
           class="{{ request()->routeIs('admin.payroll.create') ? 'active' : '' }}">
            <i class="bi bi-file-earmark-plus"></i>
            Generate Payroll
        </a>

        <a href="{{ route('admin.payroll.index') }}"
           class="{{ request()->routeIs('admin.payroll.index') || request()->routeIs('admin.payroll.show') || request()->routeIs('admin.payroll.edit') ? 'active' : '' }}">
            <i class="bi bi-receipt-cutoff"></i>
            All Payrolls
        </a>



        <a href="{{ route('admin.payroll.reports') }}"
           class="{{ request()->routeIs('admin.payroll.reports') ? 'active' : '' }}">
            <i class="bi bi-bar-chart-line"></i>
            Payroll Reports
        </a>

        <a href="{{ route('reports.index') }}"
   class="{{ request()->routeIs('reports.*') ? 'active' : '' }}">

    <i class="bi bi-bar-chart-line"></i>

    Reports

</a>

<a href="{{ route('manager.activity.index') }}"
   class="{{ request()->routeIs('manager.activity.*') ? 'active' : '' }}">

    <i class="bi bi-activity"></i>

    Manager Activity

</a>

<a href="{{ route('imports.index') }}"
   class="{{ request()->routeIs('imports.*') ? 'active' : '' }}">

    <i class="bi bi-file-earmark-arrow-up"></i>

    Import To-Do List

</a>

        <a href="{{ route('settings.index') }}"
   class="{{ request()->routeIs('settings.*') ? 'active' : '' }}">

    <i class="bi bi-gear"></i>

    Settings

</a>


<a href="{{ route('profile.edit') }}"
   class="{{ request()->routeIs('profile.*') ? 'active' : '' }}">

    <i class="bi bi-person-circle"></i>

    Profile

</a>


        @endif


        @if(auth()->user()->role->name=='Manager')

        <a href="{{ route('employees.index') }}"
           class="{{ request()->routeIs('employees.*') ? 'active' : '' }}">

            <i class="bi bi-people-fill"></i>

            Employees

        </a>



       <a href="{{ route('tasks.index') }}"
   class="{{ request()->routeIs('tasks.*') ? 'active' : '' }}">

    <i class="bi bi-list-task"></i>

    Assign Tasks

</a>

<a href="{{ route('manager.reviews.index') }}"
   class="{{ request()->routeIs('manager.reviews.*') ? 'active' : '' }}">

    <i class="bi bi-clipboard-check"></i>

    Pending Reviews

</a>

<a href="{{ route('manager.reports.index') }}"
   class="{{ request()->routeIs('manager.reports.*') ? 'active' : '' }}">

    <i class="bi bi-bar-chart-line"></i>

    Reports

</a>

<a href="{{ route('imports.index') }}"
   class="{{ request()->routeIs('imports.*') ? 'active' : '' }}">

    <i class="bi bi-file-earmark-arrow-up"></i>

    Import To-Do List

</a>

<a href="{{ route('profile.edit') }}"
   class="{{ request()->routeIs('profile.*') ? 'active' : '' }}">

    <i class="bi bi-person-circle"></i>

    Profile

</a>

        @endif

    </nav>

</aside>

    <main class="admin-main">

        <header class="topbar">

    <button type="button" class="mobile-menu-btn" onclick="toggleSidebar()">
        ☰
    </button>

    <div>
        <h5 class="mb-0 fw-bold">
            {{ $pageTitle ?? 'Dashboard' }}
        </h5>

                <small class="text-muted">
                    Welcome, {{ auth()->user()->name }}
                </small>

            </div>

           <div class="d-flex align-items-center gap-3">

    <a href="{{ route('notifications.index') }}"
       class="btn btn-light position-relative">

        <i class="bi bi-bell fs-5"></i>

        @php
            $unreadNotifications = auth()->user()
                ->notifications()
                ->where('is_read', false)
                ->count();
        @endphp

        @if($unreadNotifications > 0)

            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">

                {{ $unreadNotifications }}

            </span>

        @endif

    </a>

    <form method="POST" action="{{ route('logout') }}">

        @csrf

        <button type="submit" class="btn btn-danger">

            Logout

        </button>

    </form>

</div>

        </header>

        <section class="content">

            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show">{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
            @endif
            @if(session('warning'))
                <div class="alert alert-warning">{{ session('warning') }}</div>
            @endif
            @if($errors->any())
                <div class="alert alert-danger"><ul class="mb-0">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>
            @endif

            @yield('content')

        </section>

    </main>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

@stack('scripts')

<script>
function toggleSidebar() {
    document.querySelector('.sidebar').classList.toggle('mobile-open');
}
</script>

</body>
</html>