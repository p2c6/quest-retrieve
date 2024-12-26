import { createRouter, createWebHistory } from 'vue-router'
import guestRoutes from "@/router/guest";
import adminRoutes from "./admin";
import { useAuthStore } from '@/stores/auth';
import ROLE from '@/constants/user-role';

const routes = [
  ...guestRoutes,
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

  const isAdmin = authUser?.role_id === ROLE.IS_ADMIN;
  const isPublicUser = authUser?.role_id === ROLE.IS_PUBLIC_USER;

  const authVerifiedUserRestrictedRoutes = ['login', 'register', 'password.forgot', 'email.verification'];
  
  if (authUser && authUser.email_verified_at && authVerifiedUserRestrictedRoutes.includes(to.name) ) {
    return next({ name: 'home' });
  }

  if (authUser && authUser.email_verified_at && to.name == "password.reset" && !to.query.token ) {
    return next({ to: '/' });
  }

  
  const guestRoutes = [
    'register',
    'login',
    'password.forgot',
  ];

  if (!authUser && guestRoutes.includes(to.name)) {
    return next();
  }

  //IF PUBLIC USER AND NOT YET VERIFIED AND TRIES TO ACCESS THE HOME WHILE AUTHENTICATED
  if (authUser && !authUser.email_verified_at && isPublicUser && to.path == "/") {
    return next({name: 'email.verification'})
  }

  const authUnverifiedUserRestrictedRoutes = [
    'login',
    'register',
    'password.forgot',
    'password.reset'
  ];

  //IF AUTHENTICATED BUT NOT VERIFIED YET AND ACCESS UNVERIFIED RESTRICTED ROUTES
  if (authUser && !authUser.email_verified_at && isPublicUser && authUnverifiedUserRestrictedRoutes.includes(to.name)) {
    return next({name: 'email.verification'})
  }

  const requiresLogin = ['email.verification'];

  // IF NOT AUTHENTICATED AND ACCESS ROUTES THAT REQUIRES TO LOG IN
  if (!authUser && requiresLogin.includes(to.name)) {
    return next({name: 'login'})
  }

  const restrictedForPublicUsers = [
    'dashboard',
    'category.list',
    'category.create',
    'category.edit',
    'subcategory.list',
    'subcategory.create',
    'subcategory.edit',
    'role.list',
    'role.create',
    'role.edit',
  ];

  //IF NOT AUTHENTICATED USER AND ACCESS ADMIN ROUTES
  if (!authUser && restrictedForPublicUsers.includes(to.name)) {
    return next({ name: 'login' });
  }

  //IF PUBLIC USER AND ACCESS ADMIN ROUTES
  if (authUser && isPublicUser && restrictedForPublicUsers.includes(to.name)) {
    return next({name: 'home'})
  }

  if (isAdmin && to.path == "/") {
    return next({name: 'dashboard'})
  }

  next();

});

export default router;
