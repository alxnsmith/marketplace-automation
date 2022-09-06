@props(['title' => 'Dashboard'])
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="csrf-token" content="{{ csrf_token() }}">

  <title>{{ config('app.name', 'Laravel') }}</title>

  <!-- Fonts -->
  <link rel="stylesheet" href="https://fonts.bunny.net/css2?family=Nunito:wght@400;600;700&display=swap">

  <!-- Scripts -->
  <x-core::assets />
  @vite(['Modules/Dashboard/Resources/assets/sass/app.sass', 'Modules/Dashboard/Resources/assets/js/app.js'])
</head>

<body class="font-sans antialiased">
  <div class="min-h-screen bg-gray-100">
    <x-dashboard::layouts.navigation />
    {{-- <x-core.components.note /> --}}
    <x-core::notifications />

    <!-- Page Heading -->
    <header class="bg-white shadow">
      <div class="relative mx-auto max-w-7xl py-6 px-4 sm:px-6 lg:px-8">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
          {{ $title }}
        </h2>
      </div>
    </header>

    <!-- Page Content -->
    <main>
      <div class="py-12">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
          <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
            <div class="border-b border-gray-200 bg-white p-6">
              {{ $slot }}
            </div>
          </div>
        </div>
      </div>
    </main>
  </div>
</body>

</html>
