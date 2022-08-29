<x-dashboard-layout title="Панель инструментов">
  @foreach ($tools as $tool_template => $data)
    <div class="card-items grid grid-cols-[repeat(auto-fill,_minmax(300px,_1fr))] gap-4">
      @include($tool_template, $data)
    </div>
  @endforeach
</x-dashboard-layout>
