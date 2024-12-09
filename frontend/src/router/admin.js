import DashboardPage from "@/pages/admin/dashboard/DashboardPage.vue";
import AdminLayout from '@/components/layouts/AdminLayout.vue';

export default [
  {
    path: "/admin",
    component: AdminLayout,
    children: [
      {
        path: "",
        name: "dashboard",
        component: DashboardPage,
      },
    ],
  },
];
