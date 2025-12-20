import { createApp } from 'vue'
import { createPinia } from 'pinia'
import App from './App.vue'
import router from './router'
import '../css/app.scss'
import './plugins/axios.js'
import '@fortawesome/fontawesome-free/css/all.min.css'

const app = createApp(App)

app.use(createPinia())
app.use(router)
app.mount('#app')
