<template>
    <ol class="px-2 pt-2 flex flex-wrap">
        <li class="flex items-center">
            <router-link
                v-if="dir"
                :to="{ name: 'index' }"
                class="px-2 py-1 text-indigo-600 rounded-md hover:bg-gray-100 dark:text-gray-200 dark:hover:bg-gray-800"
            >
                Gallery
                <!-- TODO: use configured app name -->
            </router-link>
            <span
                v-else
                class="px-2 py-1 text-gray-600 dark:text-gray-200"
            >
                Gallery
            </span>
        </li>
        <template v-if="dir">
            <li
                v-for="(fragment, index) in fragments"
                :key="fragment.path"
                class="flex items-center"
            >
                <svg class="inline-block w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
                <router-link
                    v-if="index < fragments.length - 1"
                    :to="{
                        name: 'index',
                        query: { path: fragment.path },
                    }"
                    class="px-2 py-1 max-w-sm whitespace-nowrap overflow-hidden overflow-ellipsis text-indigo-600 rounded-md hover:bg-gray-100 dark:text-gray-200 dark:hover:bg-gray-800"
                >
                    {{ fragment.name }}
                </router-link>
                <span
                    v-else
                    class="px-2 py-1 max-w-sm whitespace-nowrap overflow-hidden overflow-ellipsis text-gray-600 dark:text-gray-200"
                >
                    {{ fragment.name }}
                </span>
            </li>
        </template>
    </ol>
</template>

<script>
import { computed, toRefs } from 'vue';

export default {
    props: {
        dir: String,
    },
    setup(props) {
        const { dir } = toRefs(props);

        const fragments = computed(() => {
            if (!dir.value) {
                return [];
            }
            const parts = dir.value.split('/');
            const combined = [];
            const result = [];
            parts.forEach((name, index) => {
                const path = index ? combined[index - 1] + '/' + name : name;
                combined.push(path);
                result.push({
                    name,
                    path,
                });
            });
            return result;
        });

        return {
            dir,
            fragments,
        };
    },
};
</script>
