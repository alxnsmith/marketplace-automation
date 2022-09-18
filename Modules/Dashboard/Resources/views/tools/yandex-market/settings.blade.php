<x-dashboard::layouts.master title="Настройки Yandex Market">
  @dump($settings_values)
  <div x-data>
    <form method="POST" action="{{ route('dashboard.tools.yandex-market.settings.update') }}" x-ref="form_settings">
      @csrf
      @method('patch')
      <fieldset class="mb-3">
        <div class="flex flex-col">
          <label for="field-campaign_id">campaign_id</label>
          <input type="text" name="settings[campaign_id]" id="field-campaign_id"
            value="{{ old('settings.campaign_id', $settings_values['campaign_id']) }}">
        </div>
      </fieldset>
      <div class="flex justify-between">
        <x-dashboard::button class="btn-primary">Сохранить</x-dashboard::button>
        <x-dashboard::button @click.prevent="$refs.logout_form.submit()">Выйти</x-dashboard::button>
      </div>
    </form>
    <form action="{{ route('dashboard.tools.yandex-market.logout') }}" method="POST" x-ref="logout_form">
      @csrf
    </form>
  </div>
</x-dashboard::layouts.master>
