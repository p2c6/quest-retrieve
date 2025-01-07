import ModeratorLayout from '@/components/layouts/ModeratorLayout.vue';
import DashboardPage from "@/pages/admin/dashboard/DashboardPage.vue";

export default [
  {
    path: "/moderator",
    component: ModeratorLayout,
    children: [
      {
        path: "",
        name: "moderator.dashboard",
        component: DashboardPage,
      },
    ],
  },
];

