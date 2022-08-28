@php
$has_errors = $errors->any();
$has_alerts = Session::has('alerts');
@endphp

@if ($has_errors || $has_alerts)
  <div id="alerts">
    @if ($has_errors)
      <div class="alert alert-danger">
        Ошибки:
        <ul class="ml-4">
          @foreach ($errors->all('<li class="list-disc">:message</li>') as $error)
            {!! $error !!}
          @endforeach
        </ul>
      </div>
    @endif

    @if ($has_alerts)
      @foreach (Session::get('alerts') as $alert)
        @php
          $type = is_array($alert) ? $alert['type'] : 'warning';
          $html = is_array($alert) ? $alert['html'] : $alert;
        @endphp
        <div class="alert alert-{{ $type }}">
          <p>{{ $html }}</p>
        </div>
      @endforeach
    @endif

    <script>
      document.addEventListener('DOMContentLoaded', function() {
        const removeAlert = $alert => {
          $alert.classList.remove('show');
          $alert.addEventListener('transitionend', $alert.remove);
        };
        const showAlert = ($alert, idx) => {
          const delayIn = idx * 100;
          const delayOut = 5000 - delayIn * 2;

          setTimeout(() => {
            $alert.classList.add('show');
            setTimeout(() => removeAlert($alert), delayOut);
          }, delayIn);

        }

        var alerts = document.querySelectorAll('#alerts .alert');
        [...alerts].forEach(showAlert);
      });
    </script>
  </div>
@endif
