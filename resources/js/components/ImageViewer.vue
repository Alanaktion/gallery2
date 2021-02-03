<template>
    <img :src="src" :style="style" @click="zoom" ref="img">
</template>

<script>
export default {
    props: {
        src: String,
    },
    data: () => ({
        zoomedIn: false,
    }),
    computed: {
        style() {
            if (!this.canZoom) {
                return null;
            }
            return {
                // TODO: scale proportionally to the correct dimension
                cursor: this.zoomedIn ? 'zoom-out' : 'zoom-in',
                maxWidth: this.zoomedIn ? 'none' : '100vw',
            };
        },
        canZoom() {
            // TODO: re-evaluate this on document resize
            if (!this.$refs.img) {
                return false;
            }
            return this.$refs.img.naturalWidth > document.documentElement.clientWidth
                || this.$refs.img.naturalHeight > document.documentElement.clientHeight;
        }
    },
    methods: {
        zoom() {
            if (!this.canZoom) {
                return null;
            }
            this.zoomedIn = !this.zoomedIn;
        },
    },
};
</script>
