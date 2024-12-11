import AdminLayout from '@/components/layouts/AdminLayout.vue';
import DashboardPage from "@/pages/admin/dashboard/DashboardPage.vue";
import CategoryPage from "@/pages/admin/system_configuration/category/CategoryPage.vue";

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
      {
        path: "category",
        name: "category",
        component: CategoryPage,
      },
    ],
  },
];
