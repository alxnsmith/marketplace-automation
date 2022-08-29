@props(['data' => [], 'csv' => false, 'sizes' => []]) {{-- data is csv like, or an array of arrays --}}

@php
$has_data = $csv ? count($data) > 1 : !empty($data);
@endphp

@if (!$has_data)
  <div class="alert show">Нет данных</div>
@else
  @php
    if ($csv) {
        $thead = array_values(Arr::pull($data, 0));
        $tbody = $data;
    } else {
        $thead = array_keys(head($data));
        $tbody = $data;
    }
  @endphp
  <table {{ $attributes->class(['ui-table']) }}>
    <thead>
      <tr>
        @foreach ($thead as $i => $value)
          @php
            $width = Arr::get($sizes, $i, false);
          @endphp
          <th {{ $attributes->merge(compact('width')) }}>{!! $value !!}</th>
        @endforeach
      </tr>
    </thead>
    <tbody>
      @foreach ($tbody as $row)
        <tr>
          @foreach ($row as $key => $value)
            @if (is_array($value))
              <td @class(Arr::get($value, 'class'))">{!! Arr::get($value, 'html') !!}</td>
            @else
              <td>{!! $value !!}</td>
            @endif
          @endforeach
        </tr>
      @endforeach
    </tbody>
  </table>
@endif
