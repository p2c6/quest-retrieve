import { createRouter, createWebHistory } from 'vue-router'
import userRoutes from "./user";
import { useAuthStore } from '@/stores/auth';

const routes = [
  ...userRoutes
];

const router = createRouter({
  history: createWebHistory(),
  routes,
});

router.beforeEach(async (to, from, next) => {
  const authStore = useAuthStore();

  await authStore.getUser();

  if (authStore.user && !authStore.user.email_verified_at && to.name === 'home') {
    return next({name: 'email.verification'})
  }

  next();
});

export default router;
