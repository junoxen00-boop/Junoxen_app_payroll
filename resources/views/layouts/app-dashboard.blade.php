<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Junoxen Employee Management System</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-gray-100">

<div class="flex min-h-screen">

    <!-- Sidebar -->
    <aside class="w-64 bg-slate-900 text-white">

        <div class="p-6 border-b border-slate-700">
            <h1 class="text-2xl font-bold">
                Junoxen EMS
            </h1>
        </div>

        <nav class="mt-6">

            {{-- Dashboard --}}
            <a href="#"
               class="block px-6 py-3 hover:bg-slate-800">
                Dashboard
            </a>

            {{-- Employee Menu --}}
            @if(auth()->user()->role->name == 'Employee')

                <a href="{{ route('employee.profile') }}"
                   class="block px-6 py-3 hover:bg-slate-800">
                    My Profile
                </a>

                <a href="#"
                   class="block px-6 py-3 hover:bg-slate-800">
                    My Tasks
                </a>

                <a href="#"
                   class="block px-6 py-3 hover:bg-slate-800">
                    Reports
                </a>

            @endif

            {{-- Manager Menu --}}
            @if(auth()->user()->role->name == 'Manager')

                <a href="#"
                   class="block px-6 py-3 hover:bg-slate-800">
                    Employees
                </a>

                <a href="#"
                   class="block px-6 py-3 hover:bg-slate-800">
                    Assign Tasks
                </a>

                <a href="#"
                   class="block px-6 py-3 hover:bg-slate-800">
                    Reports
                </a>

            @endif

            {{-- Admin Menu --}}
            @if(auth()->user()->role->name == 'Admin')

                <a href="#"
                   class="block px-6 py-3 hover:bg-slate-800">
                    Departments
                </a>

                <a href="#"
                   class="block px-6 py-3 hover:bg-slate-800">
                    Employees
                </a>

                <a href="#"
                   class="block px-6 py-3 hover:bg-slate-800">
                    Managers
                </a>

                <a href="#"
                   class="block px-6 py-3 hover:bg-slate-800">
                    Tasks
                </a>

                <a href="#"
                   class="block px-6 py-3 hover:bg-slate-800">
                    Reports
                </a>

            @endif

        </nav>

    </aside>

    <!-- Main Content -->
    <div class="flex-1">

        <header class="bg-white shadow">

            <div class="flex justify-between items-center px-8 py-4">

                <h2 class="text-2xl font-bold">
                    Dashboard
                </h2>

                <div class="flex items-center gap-3">

                    <span>
                        {{ auth()->user()->name }}
                    </span>

                    <form method="POST" action="{{ route('logout') }}">
                        @csrf

                        <button
                            class="bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700">
                            Logout
                        </button>

                    </form>

                </div>

            </div>

        </header>

        <main class="p-8">

            @yield('content')

        </main>

    </div>

</div>

</body>
</html>