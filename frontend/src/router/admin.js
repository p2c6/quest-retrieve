import AdminLayout from '@/components/layouts/AdminLayout.vue';
import DashboardPage from "@/pages/admin/dashboard/DashboardPage.vue";
import CategoryPage from "@/pages/admin/system_configuration/category/CategoryPage.vue";
import ListCategoryPage from "@/pages/admin/system_configuration/category/ListCategoryPage.vue";
import CreateCategoryPage from "@/pages/admin/system_configuration/category/CreateCategoryPage.vue";
import EditCategoryPage from "@/pages/admin/system_configuration/category/EditCategoryPage.vue";

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
          {
            path: "edit/:id",
            name: "category.edit",
            component: EditCategoryPage,
          },
        ],
      },
    ],
  },
];

