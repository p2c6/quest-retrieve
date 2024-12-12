import { createRouter, createWebHistory } from 'vue-router'
import userRoutes from "./user";
import adminRoutes from "./admin";
import { useAuthStore } from '@/stores/auth';
import authGuard from "@/guard/auth";

const routes = [
  ...userRoutes,
  ...adminRoutes
];

const router = createRouter({
  history: createWebHistory(),
  routes,
});

router.beforeEach(async (to, from, next) => {
  const authStore = useAuthStore();

  await authStore.getUser();
  const authUser = authStore.user;

  const redirection = authGuard(to.name, authUser);

  if (redirection) {
    next(redirection);
  } else {
    next();
  }
});

export default router;
