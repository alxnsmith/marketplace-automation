  @vite(['resources/sass/app.sass', 'resources/js/app.js'])

  <body class="font-sans antialiased">
    <div class="min-h-screen bg-gray-100">
      @include('auth::navigation')

      <!-- Page Heading -->
      <header class="bg-white shadow">
        <div class="mx-auto max-w-7xl py-6 px-4 sm:px-6 lg:px-8">
          {{ $header }}
        </div>
      </header>

      <!-- Page Content -->
      <main>
        {{ $slot }}
      </main>
    </div>
  </body>

  </html>
