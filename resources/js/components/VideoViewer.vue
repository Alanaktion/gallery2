<template>
    <video class="w-screen h-screen" controls autoplay loop>
        <source :src="src" :type="mime" />
    </video>
</template>

<script>
export default {
    props: {
        src: String,
        mime: String,
    },
};
</script>
