<select
  {{ $attributes->merge(['class' => 'block w-full border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-4 focus:ring-blue-500/30 focus:border-transparent']) }}>
  {{ $slot }}
</select>
