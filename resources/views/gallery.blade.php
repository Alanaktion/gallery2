<x-gallery-layout>
    <x-breadcrumbs :dir="$dir" />

    {{-- Directories --}}
    <div class="pt-3 pl-3 flex flex-wrap">
        @foreach($items['directories'] as $item)
            <x-directory-link :dir="$dir" :directory="$item" />
        @endforeach
    </div>

    {{-- File thumbnails --}}
    <div class="pl-3 flex flex-wrap">
        @foreach($items['files'] as $file)
            @if ($file['type'] == 'image')
                <x-image-link :dir="$dir" :file="$file" />
            @elseif ($file['type'] == 'video')
                <x-video-link :dir="$dir" :file="$file" />
            @endif
        @endforeach
    </div>
</x-gallery-layout>
