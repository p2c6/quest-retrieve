import ModeratorLayout from '@/components/layouts/ModeratorLayout.vue';
import DashboardPage from '@/pages/moderator/dashboard/DashboardPage.vue';
import ListPostPage from '@/pages/moderator/posts/ListPostPage.vue';
import PostPage from '@/pages/moderator/posts/PostPage.vue';

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
      {
        path: "posts",
        component: PostPage,
        children: [
            {
                path: "",
                name: "moderator.posts.list",
                component: ListPostPage,
            },
        ],
      },
    ],
  },
];

