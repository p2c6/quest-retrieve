import AdminLayout from '@/components/layouts/AdminLayout.vue';
import DashboardPage from "@/pages/admin/dashboard/DashboardPage.vue";
import CategoryPage from "@/pages/admin/system_configuration/category/CategoryPage.vue";
import CreateCategoryPage from "@/pages/admin/system_configuration/category/CreateCategoryPage.vue";
import ListCategoryPage from "@/pages/admin/system_configuration/category/ListCategoryPage.vue";

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
        component: CategoryPage,
        children: [
          {
            path: "",
            name: "category.list",
            component: ListCategoryPage,
          },
          {
            path: "create",
            name: "category.create",
            component: CreateCategoryPage,
          },
        ],
      },
    ],
  },
];

