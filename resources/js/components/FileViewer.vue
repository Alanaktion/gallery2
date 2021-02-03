<template>
    <div v-if="type == 'image'" class="w-screen h-screen flex">
        <image-viewer class="self-center mx-auto" :src="src" />
    </div>
    <video-viewer v-else-if="type == 'video'" :mime="mime" :src="src" />
</template>

<script>
import ImageViewer from './ImageViewer.vue';
import VideoViewer from './VideoViewer.vue';

export default {
    components: {
        ImageViewer,
        VideoViewer,
    },
    props: {
        type: {
            type: String,
            default: 'image',
        },
        mime: {
            type: String,
        },
    },
    computed: {
        src() {
            const file = location.pathname.replace(/^\/view\//, '');
            return `/src/${file}`;
        },
    },
};
</script>
