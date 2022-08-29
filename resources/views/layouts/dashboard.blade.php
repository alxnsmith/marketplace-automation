@props(['title' => 'Dashboard', 'hasBack' => false, 'backUrl' => url()->previous()])

<x-app-layout>
  <x-slot name="header">
    <h2 class="text-xl font-semibold leading-tight text-gray-800">
      {{ $title }}
    </h2>
  </x-slot>

  <div class="py-12">
    <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
      <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
        <div @class([
            'pt-16 relative' => $hasBack,
            'p-6 bg-white border-b border-gray-200',
        ])>
          @if ($hasBack)
            <x-dashboard.back-btn :backUrl="$backUrl" />
          @endif
          {{ $slot }}
        </div>
      </div>
    </div>
  </div>
</x-app-layout>

<x-global.notification />
