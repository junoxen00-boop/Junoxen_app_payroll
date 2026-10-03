<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employee Dashboard</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100">

<div class="flex min-h-screen">

    <!-- Sidebar -->
    <aside class="w-64 bg-blue-900 text-white p-5">

        <h2 class="text-2xl font-bold mb-8">
            Employee Panel
        </h2>

        <nav class="space-y-3">

            <a href="{{ route('employee.dashboard') }}" class="block hover:bg-blue-700 p-2 rounded">
                Dashboard
            </a>

            <a href="{{ route('employee.profile') }}" class="block hover:bg-blue-700 p-2 rounded">
                My Profile
            </a>

            <a href="#" class="block hover:bg-blue-700 p-2 rounded">
                My Tasks
            </a>

            <a href="#" class="block hover:bg-blue-700 p-2 rounded">
                Attendance
            </a>

            <a href="#" class="block hover:bg-blue-700 p-2 rounded">
                Leave
            </a>

        </nav>

    </aside>

    <!-- Main Content -->
    <div class="flex-1">

        <!-- Top Bar -->
        <header class="bg-white shadow p-4 flex justify-between">

            <h1 class="text-xl font-semibold">
                Employee Dashboard
            </h1>

            <div>
                {{ auth()->user()->name }}
            </div>

        </header>

        <main class="p-6">
            @yield('content')
        </main>

    </div>

</div>

</body>
</html>