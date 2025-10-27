import { ref, watchEffect, toValue} from "vue";
import axios from "axios";

export function fetch(url, options = {}) {
    const data = ref(null);
    const error = ref(null);

    const axiosFetch = async () => {

        data.value = null;
        error.value = null;

        try {
            const response = await axios.request({
                url,
                method: toValue(options).method || 'GET',
                headers: toValue(options).headers || {},
                params: toValue(options).params || {},
                data: toValue(options).data || {},
            });
            data.value = response.data;
        } catch (err) {
            error.value = err;
        }
    };

    watchEffect(async () => {
        await axiosFetch();
    });

    return { data, error };
}

