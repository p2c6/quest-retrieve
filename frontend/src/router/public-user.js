import UserLayout from "@/components/layouts/UserLayout.vue";
import EdiitAccountPage from "@/pages/user/account/EdiitAccountPage.vue";
import HomePage from "@/pages/user/home/HomePage.vue";
import CreatePostPage from "@/pages/user/posts/CreatePostPage.vue";
import EditPostPage from "@/pages/user/posts/EditPostPage.vue";
import ListPostPage from "@/pages/user/posts/ListPostPage.vue";
import PostPage from "@/pages/user/posts/PostPage.vue";

export default [
    {
        path: "/",
        component: UserLayout,
        children: [
            {
                path: "",
                name: "public-user.home",
                component: HomePage,
            },
            {
                path: "/account",
                name: "account.edit",
                component: EdiitAccountPage,
            },
            {
                path: "posts",
                component: PostPage,
                children: [
                    {
                        path: "",
                        name: "posts.list",
                        component: ListPostPage,
                    },
                    {
                        path: "create",
                        name: "posts.create",
                        component: CreatePostPage,
                    },
                    {
                        path: "edit/:id",
                        name: "posts.edit",
                        component: EditPostPage,
                    },
                ]
            },
            
        ],
    },
];
