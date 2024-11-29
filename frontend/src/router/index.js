import { createRouter, createWebHistory } from 'vue-router'
import guestRoutes from './guest';

const routes = [
  ...guestRoutes,
];

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes,
});
export default router
