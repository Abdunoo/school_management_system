import { Component, createApp } from 'vue'
import './style.css'
import App from './App.vue'
import router from './router'
import { createPinia } from 'pinia'
import 'vs-vue3-select/dist/vs-vue3-select.css'
import vSelect, { 
    VSelectProps,
    VSelectEvents,
    VSelectSlots } from 'vs-vue3-select';

const app =  createApp(App)
app.component(
    'v-select', 
    vSelect as Component<VSelectProps, VSelectEvents, VSelectSlots>
);

app.use(router)
app.use(createPinia());
app.mount('#app')