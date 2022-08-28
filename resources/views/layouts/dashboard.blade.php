@props(['title' => 'Dashboard', 'back' => false, 'backUrl' => url()->previous()])

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
            'pt-16 relative' => $back,
            'p-6 bg-white border-b border-gray-200',
        ])>
          @if ($back)
            <x-button href="{{ $backUrl }}"
              class="absolute top-3 left-3 bg-blue-300 px-2 py-1 text-2xl leading-7 hover:bg-blue-400">⬅
            </x-button>
          @endif
          {{ $slot }}
        </div>
      </div>
    </div>
  </div>
</x-app-layout>

<x-global.notification />
