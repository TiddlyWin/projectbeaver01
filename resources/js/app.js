import { createApp } from 'vue'
import App from './App.vue'
import router from './routes'
import '../css/app.scss'

createApp(App).use(router).mount('#app')
