<template>
    <div>
        <Breadcrumbs :dir="path" />

        <div
            v-if="loading"
            class="py-6 mx-3"
        >
            Loading directory&hellip;
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

            <div class="grid grid-cols-3 sm:grid-cols-4 md:grid-cols-img gap-px sm:gap-1 md:gap-2 sm:mx-3">
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

        watch(
            () => route.query.path,
            async val => {
                path.value = val;
                loading.value = true;
                try {
                    const response = await axios.get('/dir', {
                        params: {
                            dir: val,
                        },
                    });
                    items.value = response.data.items;
                } catch (e) {
                    console.error('An error occurred loading the directory.');
                }
                loading.value = false;
            },
            { immediate: true },
        );

        return {
            path,
            items,
            loading,
        };
    },
};
</script>
