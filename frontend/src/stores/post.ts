import { apiClient } from "@/config/http";
import { defineStore } from "pinia";
import { ref } from "vue";
import { useRouter } from "vue-router";

export const usePostStore = defineStore('post', () => {
    const router = useRouter();
    const posts = ref<object>({});
    const isLoading = ref<boolean | null>(null);
    const errors = ref<any | null>(null);
    const message = ref(null);
    const keyword = ref(null);

    const storePost = async(payload: any) => {
        isLoading.value = false;

        try {
            const response = await apiClient.post('/posts', payload)

            if (response.status === 201) {
                message.value = response.data.message;
                router.push({name: 'posts.list'});
            }

        } catch(error: any) {
            if (error.status == 422) {
                console.log('Validation error', error);
                errors.value = error.response.data.errors;
                return;
            }

            console.log("Error on storing post: ", error)
        }

    }

    return {
        posts,
        isLoading,
        errors,
        message,
        keyword,
        storePost,
    }
})