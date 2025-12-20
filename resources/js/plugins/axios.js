import axios from "axios";
import router from "@/router.js";
import {useUserStore} from "@/stores/user.js";

axios.defaults.withCredentials = true;
axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

axios.interceptors.response.use(
    (response) => response,
    (error) => {
        const is401 = error.response?.status === 401
        const skipRedirect = error.config?.skipAuthRedirect

        if (is401 && !skipRedirect) {
            const userStore = useUserStore()
            userStore.$reset()
            const returnTo = encodeURIComponent(router.currentRoute.value.fullPath)
            window.location.href = `/auth/eve/redirect?return=${returnTo}`
        }

        return Promise.reject(error)
    }
)
