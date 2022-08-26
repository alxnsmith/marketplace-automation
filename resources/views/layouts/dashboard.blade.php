@props(['title'=>'Dashboard', 'back'=>false])

<x-app-layout>
  <x-slot name="header">
    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
      {{ $title }}
    </h2>
  </x-slot>

  <div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
      <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div @class(['pt-16 relative'=>$back, 'p-6 bg-white border-b border-gray-200' ])>
          @if ($back)
          <x-button href="{{ url()->previous() }}"
            class="absolute top-3 left-3 bg-blue-300 hover:bg-blue-400 text-3xl px-2 py-1 leading-7">⬅
          </x-button>
          @endif
          {{$slot}}
        </div>
      </div>
    </div>
  </div>
</x-app-layout>