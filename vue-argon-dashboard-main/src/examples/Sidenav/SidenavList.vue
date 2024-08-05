<script setup>
import { useRoute } from "vue-router";

import SidenavItem from "./SidenavItem.vue";


const getRoute = () => {
  const route = useRoute();
  const routeArr = route.path.split("/");
  return routeArr[1];
};
</script>
<template>
  <div
    class="collapse navbar-collapse w-auto h-auto h-100"
    id="sidenav-collapse-main"
  >
    <ul class="navbar-nav">
      <li class="nav-item">
        <sidenav-item
          to="/home"
          :class="getRoute() === 'home' ? 'active' : ''"
          :navText="'Úvod'"
        >
          <template v-slot:icon>
            <i class="ni ni-tv-2 text-primary text-sm opacity-10"></i>
          </template>
        </sidenav-item>
      </li>

      <li class="nav-item">
        <sidenav-item
          to="/ip-addresses"
          :class="getRoute() === 'ip-addresses' ? 'active' : ''"
          :navText="'Zoznam IP adries'"
        >
          <template v-slot:icon>
            <i
              class="ni ni-calendar-grid-58 text-warning text-sm opacity-10"
            ></i>
          </template>
        </sidenav-item>
      </li>
    </ul>
  </div>
</template>
