<ol class="px-2 pt-2 flex flex-wrap">
    <li class="flex items-center">
        @if ($dir)
            <a class="px-2 py-1 text-indigo-600 rounded-md hover:bg-gray-100 dark:text-gray-200 dark:hover:bg-gray-800" href="/">
                {{ config('app.name', 'Gallery') }}
            </a>
        @else
            <span class="px-2 py-1 text-gray-600 dark:text-gray-200">
                {{ config('app.name', 'Gallery') }}
            </span>
        @endif
    </li>
    @if ($dir)
        @php
        $fragments = explode('/', $dir);
        $lastIndex = count($fragments) - 1;
        @endphp
        @foreach($fragments as $i=>$fragment)
            @php
            $path = '';
            for ($j = 0; $j < $i; $j++) {
                $path .= $fragments[$j] . '/';
            }
            $path .= $fragment;
            @endphp
            <li class="flex items-center">
                <svg class="inline-block w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                </svg>
                @if ($i != $lastIndex)
                    <a class="px-2 py-1 max-w-sm whitespace-nowrap overflow-hidden overflow-ellipsis text-indigo-600 rounded-md hover:bg-gray-100 dark:text-gray-200 dark:hover:bg-gray-800" href="/{{ $path }}">
                        {{ $fragment }}
                    </a>
                @else
                    <span class="px-2 py-1 max-w-sm whitespace-nowrap overflow-hidden overflow-ellipsis text-gray-600 dark:text-gray-200">
                        {{ $fragment }}
                    </span>
                @endif
            </li>
        @endforeach
    @endif
</ol>
