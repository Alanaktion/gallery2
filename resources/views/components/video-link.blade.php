<div class="mr-3 mb-3 inline-flex rounded-sm shadow">
    @php
    $path = $dir ? $dir . '/' . $file['name'] : $file['name'];
    @endphp
    <a class="relative group" href="/video/{{ $path }}">
        <img class="w-48 h-48 hover:bright rounded-sm border dark:border-gray-600"
            src="/thumbnail@1x/{{ $path }}"
            srcset="/thumbnail@2x/{{ $path }} 2x, /thumbnail@3x/{{ $path }} 3x"
            alt="{{ $file['name'] }}"
            loading="lazy">
        <div class="p-1 absolute bottom-px left-px right-px text-sm bg-white bg-opacity-75 backdrop-blur dark:bg-black opacity-0 group-hover:opacity-1 overflow-hidden overflow-ellipsis whitespace-nowrap">
            {{ $file['name'] }}
        </div>
    </a>
</div>
