import LoginPage from "@/pages/authentication/LoginPage.vue";
import RegisterPage from "@/pages/authentication/RegisterPage.vue";
import EmailVerificationPage from "@/pages/authentication/EmailVerificationPage.vue";
import VerifyEmailPage from "@/pages/authentication/VerifyEmailPage.vue";
import ForgotPasswordPage from "@/pages/authentication/ForgotPasswordPage.vue";
import ResetPasswordPage from "@/pages/authentication/ResetPasswordPage.vue";
import UserLayout from '@/components/layouts/UserLayout.vue';
import HomePage from "@/pages/guest/HomePage.vue";

export default [
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
      {
        path: "/email-verification",
        name: "email.verification",
        component: EmailVerificationPage,
      },
      {
        path: "/verify-email",
        name: "verify.email",
        component: VerifyEmailPage,
      },
      {
        path: "/forgot-password",
        name: "password.forgot",
        component: ForgotPasswordPage,
      },
      {
        path: "/reset-password",
        name: "password.reset",
        component: ResetPasswordPage,
      },
    ],
  },
];
