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

    const getAllPublicPost = async(page = 1) => {
        isLoading.value = true;
        try {
            const response = await apiClient.get(`/public/posts?page=${page}&filter[keyword]=${keyword.value ?? ""}`);

            posts.value = response.data

            isLoading.value = false;
        } catch(error) {
            console.log("Error fetching all public post: ", error);
        } finally {
            isLoading.value = null;
        }
    }

    const getAllUserPosts = async(page = 1) => {
        isLoading.value = true;
        try {
            const response = await apiClient.get(`/posts?page=${page}&filter[keyword]=${keyword.value ?? ""}`);

            posts.value = response.data

            isLoading.value = false;
        } catch(error) {
            console.log("Error fetching all user post: ", error);
        } finally {
            isLoading.value = null;
        }
    }

    const getAllForApprovalPost = async(page = 1) => {
        isLoading.value = true;
        try {
            const response = await apiClient.get(`/approval/posts?page=${page}&filter[keyword]=${keyword.value ?? ""}`);

            posts.value = response.data

            isLoading.value = false;
        } catch(error) {
            console.log("Error fetching all user post: ", error);
        } finally {
            isLoading.value = null;
        }
    }

    const getUserPost = async(id: any) => {
        isLoading.value = true;
        try {
            const response = await apiClient.get(`/posts/${id}`);
            
            if (response.status === 200) {
                return response.data.data;
            }

        } catch(error) {
            console.log("Error fetching user post: ", error);
        } finally {
            isLoading.value = null;
        }
    }

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

    const updatePost = async(payload: any) => {
        isLoading.value = false;

        try {
            const response = await apiClient.put(`/posts/${payload.id}`, payload)

            if (response.status === 200) {
                message.value = response.data.message;
                router.push({name: 'posts.list'});
            }

        } catch(error: any) {
            if (error.status == 422) {
                console.log('Validation error', error);
                errors.value = error.response.data.errors;
                return;
            }

            if (error.status == 409) {
                console.log('Validation error', error);
                errors.value = error.response.data;
                router.push({name: 'posts.list'});
                return;
            }


            console.log("Error on storing post: ", error)
        }
    }

    const deletePost = async(id: any) => {
        isLoading.value = false;

        try {
            const response = await apiClient.delete(`/posts/${id}`)

            if (response.status === 200) {
                message.value = response.data.message;
                
                getAllUserPosts()

                router.push({name: 'posts.list'});
            }

        } catch(error: any) {
            if (error.status == 409) {
                console.log('Validation error', error);
                errors.value = error.response.data;
                return;
            }

            console.log("Error on deleting post: ", error)
        }

    }

    const markAsDonePost = async(id: any) => {
        isLoading.value = false;

        try {
            const response = await apiClient.put(`/posts/${id}/mark-as-done`)

            if (response.status === 200) {
                message.value = response.data.message;
                
                getAllUserPosts()

                router.push({name: 'posts.list'});
            }

        } catch(error: any) {
            if (error.status == 409) {
                console.log('Validation error', error);
                errors.value = error.response.data;
                return;
            }

            console.log("Error on marking as done post: ", error)
        }

    }

    const approvePost = async(id: any) => {
        isLoading.value = false;

        try {
            const response = await apiClient.put(`/approval/posts/${id}/approve`)

            if (response.status === 200) {
                message.value = response.data.message;
                
                getAllForApprovalPost()

                router.push({name: 'moderator.posts.list'});
            }

        } catch(error: any) {
            if (error.status == 409) {
                console.log('Validation error', error);
                errors.value = error.response.data;
                return;
            }

            console.log("Error on approving post: ", error)
        }

    }

    const rejectPost = async(id: any) => {
        isLoading.value = false;

        try {
            const response = await apiClient.put(`/approval/posts/${id}/reject`)

            if (response.status === 200) {
                message.value = response.data.message;
                
                getAllForApprovalPost()

                router.push({name: 'moderator.posts.list'});
            }

        } catch(error: any) {
            if (error.status == 409) {
                console.log('Validation error', error);
                errors.value = error.response.data;
                return;
            }

            console.log("Error on approving post: ", error)
        }
    }

    const requestClaimOrReturnPost = async(payload: any) => {
        isLoading.value = false;

        try {
            let url = `/public/posts/${payload.id}/claim`;
        
            if (payload.type == 'Lost') {
                url = `/public/posts/${payload.id}/return`;
            }

            const response = await apiClient.post(url, payload)
    
            if (response?.status === 200) {
                message.value = response.data.message;

                router.push({name: 'home'});
            }

        } catch(error: any) {
            if (error.status == 409) {
                console.log('Validation error', error);
                errors.value = error.response.data;
                return;
            }

            console.log("Error on requesting claim or return post: ", error)
        }

    }

    return {
        posts,
        isLoading,
        errors,
        message,
        keyword,
        storePost,
        getAllUserPosts,
        getUserPost,
        updatePost,
        deletePost,
        markAsDonePost,
        getAllForApprovalPost,
        approvePost,
        rejectPost,
        getAllPublicPost,
        requestClaimOrReturnPost,
    }
})