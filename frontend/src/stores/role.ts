import { apiClient } from "@/config/http";
import type { DeleteRole, GetRole, StoreRole, UpdateRole } from "@/types/role-types";
import { defineStore } from "pinia";
import { ref } from "vue";
import { useRouter } from "vue-router";

export const useRoleStore = defineStore('role', () => {
    const router = useRouter();
    const roles = ref<object>({});
    const isLoading = ref<boolean | null>(null);
    const errors = ref<any | null>(null);
    const message = ref(null);
    const keyword = ref(null);
    const rolesDropdown = ref(null);

    const getAllRoles = async(page = 1) => {
        isLoading.value = true;

        try {
            const response = await apiClient.get(`/roles?page=${page}&filter[name]=${keyword.value ?? ""}`);
            
            roles.value =  response.data;

            isLoading.value = false;

        } catch(error) {
            console.log("Error fetching roles: ", error);
        } finally {
            isLoading.value = null;
        }
    }

    const getAllRolesDropdown = async() => {
        isLoading.value = true;

        try {
            const response = await apiClient.get(`/roles/dropdown`);
            
            rolesDropdown.value =  response.data;

            isLoading.value = false;

        } catch(error) {
            console.log("Error fetching roles: ", error);
        } finally {
            isLoading.value = null;
        }
    }

    const getRole = async(id: GetRole) => {
        isLoading.value = true;

        try {
            const response = await apiClient.get(`/roles/${id}`);
            
            if (response.status === 200) {
                return response.data.data;
            }

        } catch(error) {
            console.log("Error fetching roles: ", error);
        } finally {
            isLoading.value = null;
        }
    }

    const storeRole = async(payload: StoreRole) => {
        isLoading.value = false;

        try {
            const response = await apiClient.post('/roles', payload)

            if (response.status === 201) {
                message.value = response.data.message;
                router.push({name: 'role.list'});
            }

        } catch(error: any) {
            if (error.status == 422) {
                console.log('Validation error', error);
                errors.value = error.response.data.errors;
                return;
            }

            console.log("Error on storing Role: ", error)
        }

    }

    const updateRole = async(payload: UpdateRole) => {
        isLoading.value = false;

        try {
            const response = await apiClient.put(`/roles/${payload.id}`, payload)

            if (response.status === 200) {
                message.value = response.data.message;
                router.push({name: 'role.list'});
            }

        } catch(error: any) {
            if (error.status == 422) {
                console.log('Validation error', error);
                errors.value = error.response.data.errors;
                return;
            }

            console.log("Error on updating role: ", error)
        }

    }

    const deleteRole = async(id: DeleteRole) => {
        isLoading.value = false;

        try {
            const response = await apiClient.delete(`/roles/${id}`)

            if (response.status === 200) {
                message.value = response.data.message;
                
                getAllRoles()

                router.push({name: 'role.list'});
            }

        } catch(error: any) {
            if (error.status == 409) {
                console.log('Validation error', error);
                errors.value = error.response.data;
                return;
            }

            console.log("Error on deleting role: ", error)
        }

    }

    return {
        roles,
        message,
        errors,
        isLoading,
        keyword,
        rolesDropdown,

        getAllRoles,
        getRole,
        storeRole,
        updateRole,
        deleteRole,
        getAllRolesDropdown
    }
});