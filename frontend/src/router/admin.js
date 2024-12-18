import AdminLayout from '@/components/layouts/AdminLayout.vue';
import DashboardPage from "@/pages/admin/dashboard/DashboardPage.vue";
import CategoryPage from "@/pages/admin/system_configuration/category/CategoryPage.vue";
import ListCategoryPage from "@/pages/admin/system_configuration/category/ListCategoryPage.vue";
import CreateCategoryPage from "@/pages/admin/system_configuration/category/CreateCategoryPage.vue";
import EditCategoryPage from "@/pages/admin/system_configuration/category/EditCategoryPage.vue";
import SubcategoryPage from '@/pages/admin/system_configuration/subcategory/SubcategoryPage.vue';
import ListSubcategoryPage from '@/pages/admin/system_configuration/subcategory/ListSubcategoryPage.vue';
import CreateSubcategoryPage from '@/pages/admin/system_configuration/subcategory/CreateSubcategoryPage.vue';
import EditSubcategoryPage from '@/pages/admin/system_configuration/subcategory/EditSubcategoryPage.vue';

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
      {
        path: "subcategory",
        component: SubcategoryPage,
        children: [
          {
            path: "",
            name: "subcategory.list",
            component: ListSubcategoryPage,
          },
          {
            path: "create",
            name: "subcategory.create",
            component: CreateSubcategoryPage,
          },
          {
            path: "edit/:id",
            name: "subcategory.edit",
            component: EditSubcategoryPage,
          },
        ],
      },
    ],
  },
];

