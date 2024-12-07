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
  const authenticatedUser = authStore.user;
  const notYetVerified = authenticatedUser && !authenticatedUser.email_verified_at;
  const forbbidenRoutes = ['home', 'register', 'login'];

  if (notYetVerified && forbbidenRoutes.includes(to.name)) {
    return next({name: 'email.verification'})
  }

  if (authenticatedUser && authenticatedUser.email_verified_at &&  to.name === "login") {
    return next({name: 'home'})
  }

  if (authenticatedUser && authenticatedUser.email_verified_at &&  to.name === "register") {
    return next({name: 'home'})
  }

  if(!notYetVerified && to.name === "email.verification") {
    return next({name: 'home'})
  }

  if (!notYetVerified && to.name === "verify.email") {
    return next({name: 'home' });
  }
  
  next();
});

export default router;
