import { createRouter, createWebHistory } from 'vue-router'
import userRoutes from "./user";

const routes = [
  ...userRoutes
];

const router = createRouter({
  history: createWebHistory(),
  routes,
});

export default router;
