import { apiClient } from "@/config/http";
import { defineStore } from "pinia";
import { ref } from "vue";
import { useRouter } from "vue-router";


export const useUserStore = defineStore('user', () => {
    const router = useRouter();
    const users = ref<object>({});
    const isLoading = ref<boolean | null>(null);
    const errors = ref<any | null>(null);
    const message = ref(null);
    const keyword = ref(null);
    const rolesDropdown = ref(null);

    const getAllUsers = async(page = 1) => {
        isLoading.value = true;

        try {
            const response = await apiClient.get(`/users?page=${page}&filter[keyword]=${keyword.value ?? ""}`);
            
            users.value =  response.data;

            isLoading.value = false;

        } catch(error) {
            console.log("Error fetching users: ", error);
        } finally {
            isLoading.value = null;
        }
    }

    return {
        /*
            @Variables
         */
        users,
        errors,
        message,
        keyword,
        isLoading,

        /*
            @Functions
        */
        getAllUsers
    }
});