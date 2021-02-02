<div class="ratio ratio-1x1 sm:rounded-sm sm:shadow">
    @php
    $path = $dir ? $dir . '/' . $file['name'] : $file['name'];
    $encoded = implode('/', array_map('rawurlencode', explode('/', $path)));
    @endphp
    <a class="relative group bg-gray-100 dark:bg-gray-800" href="/{{ $type == 'video' ? 'video' : 'src' }}/{{ $encoded }}">
        <img class="group-hover:bright sm:rounded-sm"
            src="/thumbnail@1x/{{ $encoded }}"
            srcset="/thumbnail@2x/{{ $encoded }} 2x, /thumbnail@3x/{{ $encoded }} 3x"
            alt="{{ $file['name'] }}"
            loading="lazy">
        <div class="p-1 absolute bottom-0 left-0 right-0 text-sm bg-white bg-opacity-75 backdrop-blur dark:bg-black opacity-0 group-hover:opacity-100 overflow-hidden overflow-ellipsis whitespace-nowrap z-10">
            {{ $file['name'] }}
        </div>
        @if ($type == 'video')
            <div class="p-1 absolute top-1 right-1 rounded-full bg-white bg-opacity-75 backdrop-blur dark:bg-black">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                </svg>
            </div>
        @endif
    </a>
</div>
