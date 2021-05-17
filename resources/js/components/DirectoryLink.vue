<template>
    <div class="mr-3 mb-3 inline-flex rounded-md shadow">
        <router-link
            :to="{
                name: 'index',
                query: { path: dir ? `${dir}/${name}` : name }
            }"
            class="inline-flex items-center justify-center px-5 py-3 border border-transparent text-base font-medium rounded-md text-indigo-600 bg-white hover:bg-indigo-50 dark:text-indigo-400 dark:hover:text-white dark:bg-gray-800 dark:hover:bg-gray-700"
        >
            {{ name }}
        </router-link>
    </div>
</template>

<script>
import { toRefs } from 'vue';

export default {
    props: {
        dir: String,
        name: String,
    },
    setup(props) {
        const { name, dir } = toRefs(props);
        return {
            name,
            dir,
        };
    },
};
</script>
