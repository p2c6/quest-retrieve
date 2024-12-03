import { createRouter, createWebHistory } from 'vue-router'
import LoginPage from "@/pages/authentication/LoginPage.vue";
import RegisterPage from "@/pages/authentication/RegisterPage.vue";
import HomePage from "@/pages/user/home/HomePage.vue";
import UserLayout from '@/components/layouts/UserLayout.vue';
import { useAuthStore } from "@/stores/auth.ts";

const routes = [
  {
    path: "/",
    component: UserLayout,
    children: [
      {
        path: "",
        name: "home",
        component: HomePage,
      },
      {
        path: "/login",
        name: "login",
        component: LoginPage,
      },
      {
        path: "/register",
        name: "register",
        component: RegisterPage,
      },
    ],
  },
];

const router = createRouter({
  history: createWebHistory(),
  routes,
});

export default router;
