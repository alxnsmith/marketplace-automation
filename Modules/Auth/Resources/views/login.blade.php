<x-auth::guest-layout>
  <x-auth::card>
    <x-slot name="logo">
      <a href="/">
        <x-core::application-logo class="h-20 w-20 fill-current text-gray-500" />
      </a>
    </x-slot>

    <!-- Session Status -->
    <x-auth::session-status class="mb-4" :status="session('status')" />

    <!-- Validation Errors -->
    <x-auth::validation-errors class="mb-4" :errors="$errors" />

    <form method="POST" action="{{ route('login') }}">
      @csrf

      <!-- Email Address -->
      <div>
        <x-auth::label for="email" :value="__('Email')" />

        <input id="email" class="mt-1 block w-full" type="email" name="email" :value="old('email')" required
          autofocus />
      </div>

      <!-- Password -->
      <div class="mt-4">
        <x-auth::label for="password" :value="__('Password')" />

        <input id="password" class="mt-1 block w-full" type="password" name="password" required
          autocomplete="current-password" />
      </div>

      <!-- Remember Me -->
      <div class="mt-4 block">
        <label for="remember_me" class="inline-flex items-center">
          <input id="remember_me" type="checkbox"
            class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
            name="remember">
          <span class="ml-2 text-sm text-gray-600">{{ __('Remember me') }}</span>
        </label>
      </div>

      <div class="mt-4 flex items-center justify-end">
        @if (Route::has('password.request'))
          <a class="text-sm text-gray-600 underline hover:text-gray-900" href="{{ route('password.request') }}">
            {{ __('Forgot your password?') }}
          </a>
        @endif

        <x-auth::button class="ml-3">
          {{ __('Log in') }}
          </x-button>
      </div>
    </form>
    </x-auth-card>
    </x-guest-layout>
