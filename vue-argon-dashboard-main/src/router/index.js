import { createRouter, createWebHistory } from "vue-router";
import Dashboard from "../views/Dashboard.vue";
import Tables from "../views/Tables.vue";

const routes = [
  {
    path: "/",
    name: "/",
    redirect: "/home",
  },
  {
    path: "/home",
    name: "Úvod",
    component: Dashboard,
  },
  {
    path: "/ip-addresses",
    name: "Zoznam adries",
    component: Tables,
  },
];

const router = createRouter({
  history: createWebHistory(process.env.BASE_URL),
  routes,
  linkActiveClass: "active",
});

export default router;
