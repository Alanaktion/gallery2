<template>
    <div>
        <Breadcrumbs :dir="path" />

        <div
            v-if="loading"
            class="grid grid-cols-3 sm:grid-cols-4 md:grid-cols-img gap-px sm:gap-1 md:gap-2 sm:mx-3 sm:mb-3"
        >
            <div class="ratio ratio-1x1 sm:rounded-sm sm:shadow animate-pulse bg-gray-200 dark:bg-gray-700">
                <span class="sr-only">Loading directory&hellip;</span>
            </div>
            <div class="ratio ratio-1x1 sm:rounded-sm sm:shadow animate-pulse bg-gray-100 dark:bg-gray-800"></div>
            <div class="ratio ratio-1x1 sm:rounded-sm sm:shadow animate-pulse bg-gray-50 dark:bg-gray-800/50"></div>
        </div>

        <template v-else-if="items">
            <div class="pt-3 pl-3 flex flex-wrap">
                <DirectoryLink
                    v-for="dir in items.directories"
                    :key="dir.name"
                    :dir="path"
                    :name="dir.name"
                />
            </div>

            <div
                v-if="!hasItems"
                class="text-center py-8 md:py-12"
            >
                <h2 class="text-lg font-medium text-gray-500">No items found</h2>
            </div>
            <div
                v-else
                class="grid grid-cols-3 sm:grid-cols-4 md:grid-cols-img gap-px sm:gap-1 md:gap-2 sm:mx-3 sm:mb-3"
            >
                <template v-for="file in items.files" :key="file">
                    <ImageLink
                        v-if="file.type === 'image' || file.type === 'video'"
                        :dir="path"
                        :name="file.name"
                        :type="file.type"
                        :size="file.size"
                    />
                </template>
            </div>
        </template>
    </div>
</template>

<script>
import { ref, watch } from 'vue';
import { useRoute } from 'vue-router';
import Breadcrumbs from '../components/Breadcrumbs.vue';
import DirectoryLink from '../components/DirectoryLink.vue';
import ImageLink from '../components/ImageLink.vue';

export default {
    components: {
        Breadcrumbs,
        DirectoryLink,
        ImageLink,
    },
    setup() {
        const route = useRoute();

        const path = ref(route.query.path);
        const items = ref(null);
        const loading = ref(false);
        const hasItems = ref(false);

        const CancelToken = axios.CancelToken;
        let cancel;

        watch(
            () => route.query.path,
            async val => {
                path.value = val;
                loading.value = true;
                if (cancel) {
                    cancel('Navigated away from the directory');
                }
                try {
                    hasItems.value = false;
                    const response = await axios.get('/dir', {
                        params: {
                            dir: val,
                        },
                        cancelToken: new CancelToken(c => cancel = c),
                    });
                    items.value = response.data.items;
                    items.value.files.forEach(file => {
                        if (file.type === 'image' || file.type === 'video') {
                            hasItems.value = true;
                        }
                    });
                } catch (e) {
                    if (!e.__CANCEL__) {
                        console.error('An error occurred loading the directory.');
                    }
                }
                loading.value = false;
            },
            { immediate: true },
        );

        return {
            path,
            items,
            loading,
            hasItems,
        };
    },
};
</script>
