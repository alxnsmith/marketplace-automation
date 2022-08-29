@php
$has_errors = $errors->any();
$has_notifies = Session::has('notifies');
@endphp

@if ($has_errors || $has_notifies)
  <div id="notifies">
    @if ($has_errors)
      <div class="notify notify-danger">
        Ошибки:
        <ul class="ml-4">
          @foreach ($errors->all('<li class="list-disc">:message</li>') as $error)
            {!! $error !!}
          @endforeach
        </ul>
      </div>
    @endif

    @if ($has_notifies)
      @foreach (Session::get('notifies') as $notify)
        @php
          $type = is_array($notify) ? $notify['type'] : 'warning';
          $html = is_array($notify) ? $notify['html'] : $notify;
        @endphp
        <div class="notify notify-{{ $type }}">
          <p>{{ $html }}</p>
        </div>
      @endforeach
    @endif

    <script>
      document.addEventListener('DOMContentLoaded', function() {
        const removeNotify = $notify => {
          $notify.classList.remove('show');
          $notify.addEventListener('transitionend', $notify.remove);
        };
        const showNotify = ($notify, idx) => {
          const delayIn = idx * 100;
          const delayOut = 5000 - delayIn * 2;

          setTimeout(() => {
            $notify.classList.add('show');
            setTimeout(() => removeNotify($notify), delayOut);
          }, delayIn);

        }

        var notifies = document.querySelectorAll('#notifies .notify');
        console.log(notifies);
        [...notifies].forEach(showNotify);
      });
    </script>
  </div>
@endif
