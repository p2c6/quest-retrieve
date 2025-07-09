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
    const column = ref<string | null>(null);
    const currentColumn = ref('category_name');
    const currentDirection = ref('asc');

    const getAllUsers = async(page = 1) => {
        isLoading.value = true;

        try {
            const response = await apiClient.get(`/users?page=${page}&sort=${column.value ?? ""}&filter[keyword]=${keyword.value ?? ""}`);
            
            users.value =  response.data;

            isLoading.value = false;

        } catch(error) {
            console.log("Error fetching users: ", error);
        } finally {
            isLoading.value = null;
        }
    }

    const sort = async(columnName: string) => {
        if (currentColumn.value === columnName) {
            currentDirection.value = currentDirection.value === 'asc' ? 'desc' : 'asc';
        } else {
            currentColumn.value = columnName;
            currentDirection.value = 'asc';
        }

        const prefix = currentDirection.value === 'desc' ? '-' : '';
        column.value = `${prefix}${columnName}`;
    }

    const getUser = async(id: any) => {
            isLoading.value = true;
    
            try {
                const response = await apiClient.get(`/users/${id}`);
                
                if (response.status === 200) {
                    return response.data.data;
                }
    
            } catch(error) {
                console.log("Error fetching user: ", error);
            } finally {
                isLoading.value = null;
            }
        }

    const storeUser = async(payload: any) => {
        isLoading.value = false;
        
        try { 
            const response = await apiClient.post('/users', payload);

            if (response.status === 201) {
                message.value = response.data.message;
                router.push({name: 'users.list'});
            }

        } catch(error: any) {
            if (error.status === 422) {
                console.log('Validation error', error);
                errors.value = error.response.data.errors;
                return;
            }
            
            console.log("Error on storing user: " , error);
        } finally {
            isLoading.value = false;
        }
    }

    const updateUser = async(payload: any) => {
        isLoading.value = false;

        try {

            if (!payload.password || payload.length === 0 || payload.password === '') {
                delete payload.password;
            }

            const response = await apiClient.put(`/users/${payload.id}`, payload)

            if (response.status === 200) {
                message.value = response.data.message;
                router.push({name: 'users.list'});
            }

        } catch(error: any) {
            if (error.status == 422) {
                console.log('Validation error', error);
                errors.value = error.response.data.errors;
                return;
            }

            console.log("Error on updating user: ", error)
        }
    }

    const deleteUser = async(id: any) => {
        isLoading.value = false;

        try {
            const response = await apiClient.delete(`/users/${id}`)

            if (response.status === 200) {
                message.value = response.data.message;
                
                getAllUsers()

                router.push({name: 'users.list'});
            }

        } catch(error: any) {
            if (error.status == 409) {
                console.log('Validation error', error);
                errors.value = error.response.data;
                return;
            }

            console.log("Error on deleting user: ", error)
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
        column,

        /*
            @Functions
        */
        getAllUsers,
        storeUser,
        getUser,
        updateUser,
        deleteUser,
        sort,
    }
});