import axios from "axios";

const baseConfig = {
    withCredentials: true,
};

const apiClient = axios.create({
    baseURL: "http://localhost:8000/api/v1",
    ...baseConfig
});

const webClient = axios.create({
    baseURL: "http://localhost:8000",
    ...baseConfig 
});

apiClient.interceptors.request.use((config) => {

    if (config.method !== "GET") {
        webClient.get('/sanctum/csrf-cookie')
    }
    
    return config;
})



export { apiClient, webClient };
