<x-dashboard::layouts.master title="Настройки Yandex Market">
  @dump($settings_values)
  <form method="POST" action="{{ route('dashboard.tools.yandex-market.settings.update') }}">
    @csrf
    @method('patch')
    <fieldset class="mb-3">
      <div class="flex flex-col">
        <label for="field-campaign_id">campaign_id</label>
        <input type="text" name="settings[campaign_id]" id="field-campaign_id"
          value="{{ old('settings.campaign_id', $settings_values['campaign_id']) }}">
      </div>
    </fieldset>
    <x-dashboard::button class="btn-primary">Сохранить</x-button>
  </form>
</x-dashboard::layouts.master>
