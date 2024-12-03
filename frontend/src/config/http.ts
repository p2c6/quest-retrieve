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

apiClient.interceptors.request.use(async (config) => {
    if (config.method !== "get" && !document.cookie.includes("XSRF-TOKEN")) {
        await webClient.get("/sanctum/csrf-cookie");
    }

    return config;
}, (error) => {
    return Promise.reject(error);
});



export { apiClient, webClient };
