<div class="mr-3 mb-3 inline-flex rounded-md shadow">
    @php
    $path = $dir ? $dir . '/' . $directory['name'] : $directory['name'];
    @endphp
    <a href="/{{ $path }}" class="inline-flex items-center justify-center px-5 py-3 border border-transparent text-base font-medium rounded-md text-indigo-600 bg-white hover:bg-indigo-50 dark:text-indigo-400 dark:hover:text-white dark:bg-gray-800 dark:hover:bg-gray-700">
        {{ $directory['name'] }}
    </a>
</div>
