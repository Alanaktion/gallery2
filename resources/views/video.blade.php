<x-gallery-layout>
    <video class="w-screen h-screen" controls autoplay loop>
        <source src="/src/{{ implode('/', array_map('rawurlencode', explode('/', $path))) }}" type="{{ $mime }}" />
    </video>
</x-gallery-layout>
