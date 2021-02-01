<div class="ratio ratio-1x1 sm:rounded-sm sm:shadow">
    @php
    $path = $dir ? $dir . '/' . $file['name'] : $file['name'];
    @endphp
    <a class="relative group" href="/video/{{ $path }}">
        <img class="hover:bright sm:rounded-sm"
            src="/thumbnail@1x/{{ $path }}"
            srcset="/thumbnail@2x/{{ $path }} 2x, /thumbnail@3x/{{ $path }} 3x"
            alt="{{ $file['name'] }}"
            loading="lazy">
        <div class="p-1 absolute bottom-0 left-0 right-0 text-sm bg-white bg-opacity-75 backdrop-blur dark:bg-black opacity-0 group-hover:opacity-100 overflow-hidden overflow-ellipsis whitespace-nowrap z-10">
            {{ $file['name'] }}
        </div>
    </a>
</div>
