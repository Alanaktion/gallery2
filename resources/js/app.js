require('./bootstrap');

import { createApp } from 'vue';
import { createRouter, createWebHashHistory } from 'vue-router';
import GalleryApp from './components/GalleryApp.vue';

import Index from './routes/Index.vue';
// import File from './routes/File.vue';

const routes = [
    {
        path: '/',
        name: 'index',
        component: Index,
    },
    // {
    //     path: '/file',
    //     name: 'file',
    //     component: File,
    // },
];

const router = createRouter({
    history: createWebHashHistory(),
    routes,
});

const app = createApp({
    components: {
        GalleryApp,
    },
});

app.use(router);

app.mount('#app');
