require('./bootstrap');

import { createApp } from 'vue';
import FileViewer from './components/FileViewer.vue';

const app = createApp({
    //
});

app.component('file-viewer', FileViewer);

app.mount('#app');
