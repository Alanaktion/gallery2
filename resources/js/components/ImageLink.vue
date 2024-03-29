<template>
    <div class="ratio ratio-1x1 sm:rounded-sm sm:shadow" ref="cell">
        <a
            class="relative group bg-gray-100 dark:bg-gray-800"
            :href="type === 'video' ? `/video/${encoded}` : `/src/${encoded}`"
        >
            <img
                class="group-hover:bright sm:rounded-sm"
                :src="error ? '/img/thumbnail-error.svg' : `/thumbnail@1x/${encoded}`"
                :srcset="error ? '' : `/thumbnail@2x/${encoded} 2x, /thumbnail@3x/${encoded} 3x`"
                :alt="name"
                ref="img"
                loading="lazy"
                v-show="show"
            >
            <div class="p-1 absolute bottom-0 left-0 right-0 text-sm bg-white bg-opacity-75 backdrop-blur dark:bg-black opacity-0 group-hover:opacity-100 overflow-hidden overflow-ellipsis whitespace-nowrap z-10">
                {{ name }}
            </div>
            <div
                v-if="type === 'video'"
                class="p-1 absolute top-1 right-1 rounded-full bg-white bg-opacity-75 backdrop-blur dark:bg-black"
            >
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
                </svg>
            </div>
        </a>
    </div>
</template>

<script>
import { computed, onMounted, ref, toRefs, watch } from 'vue';

export default {
    props: {
        dir: String,
        name: String,
        type: String,
        size: Number,
    },
    setup(props) {
        const { dir, name, type } = toRefs(props);
        const show = ref(false);
        const error = ref(false);

        // Compute encoded file path
        const encoded = computed(() => {
            const path = dir.value ? `${dir.value}/${name.value}` : name.value;
            return path.split('/').map(p => encodeURI(p)).join('/');
        });

        watch(
            name,
            (val) => {
                if (val) {
                    error.value = false;
                }
            },
        );

        // Trigger image loading via IntersectionObserver, show error image on failed loads
        const cell = ref(null);
        const img = ref(null);
        onMounted(() => {
            const io = new IntersectionObserver((entries) => {
                entries.forEach((entry) => {
                    if (entry.isIntersecting) {
                        io.unobserve(entry.target);
                        show.value = true;
                        img.value.addEventListener('error', () => {
                            error.value = true;
                        });
                    }
                });
            }, { rootMargin: '0px 0px 200px 0px' });
            io.observe(cell.value);
        });

        return {
            name,
            type,
            encoded,
            cell,
            img,
            show,
            error,
        };
    }
};
</script>
