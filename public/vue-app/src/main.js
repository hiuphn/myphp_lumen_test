import { createApp } from 'vue'
import './style.css'
import App from './App.vue'
import axios from 'axios';
import router from './router';

axios.defaults.baseURL = 'http://localhost:8000';

const app = createApp(App);

app.use(router);
app.mount('#app');
