@props(['data' => [], 'csv' => false]) {{-- data is csv like, or an array of arrays --}}

@php
$has_data = $csv ? count($data) > 1 : !empty($data);
@endphp

@if (!$has_data)
  <div class="alert show">Нет данных</div>
@else
  @php
    if ($csv) {
        $thead = array_values($data[0]);
        $tbody = array_slice($data, 1);
    } else {
        $thead = array_keys($data[0]);
        $tbody = $data;
    }
  @endphp
  <table {{ $attributes->class(['ui-table']) }}>
    <thead>
      <tr>
        @foreach ($thead as $value)
          <th>{{ $value }}</th>
        @endforeach
      </tr>
    </thead>
    <tbody>
      @foreach ($tbody as $row)
        <tr>
          @foreach ($row as $key => $value)
            <td>{{ $value }}</td>
          @endforeach
        </tr>
      @endforeach
    </tbody>
  </table>
@endif
