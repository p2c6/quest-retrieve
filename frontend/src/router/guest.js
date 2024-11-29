import GuestLayout from '@/components/layouts/GuestLayout.vue';
import Login from '../components/authentication/Login.vue';
import Register from '@/components/authentication/Register.vue';

export default [
  {
    path: '/',
    redirect: { name: 'login' }, 
    component: GuestLayout,
    children: [
      {
        path: '/login',
        name: 'login',
        component: Login,
      },
      {
        path: '/register',
        name: 'register',
        component: Register,
      }
    ]
  }
];
