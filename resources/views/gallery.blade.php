<x-gallery-layout :title="$title">
    <x-breadcrumbs :dir="$dir" />

    {{-- Directories --}}
    <div class="pt-3 pl-3 flex flex-wrap">
        @foreach($items['directories'] as $item)
            <x-directory-link :dir="$dir" :directory="$item" />
        @endforeach
    </div>

    {{-- File thumbnails --}}
    <div class="grid grid-cols-3 sm:grid-cols-4 md:grid-cols-img gap-px sm:gap-1 md:gap-2 sm:mx-3">
        @foreach($items['files'] as $file)
            @if ($file['type'] == 'image' || $file['type'] == 'video')
                <x-image-link :dir="$dir" :file="$file" :type="$file['type']" />
            @endif
        @endforeach
    </div>
</x-gallery-layout>
