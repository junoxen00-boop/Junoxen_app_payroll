<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <meta name="robots" content="noindex, nofollow">

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>JUNOXEN Employee Management</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body>

    {{ $slot }}

</body>

</html>