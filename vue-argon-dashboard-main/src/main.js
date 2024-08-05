import { createApp } from "vue";
import App from "./App.vue";
import store from "./store";
import router from "./router";
import "./assets/css/nucleo-icons.css";
import "./assets/css/nucleo-svg.css";
import ArgonDashboard from "./argon-dashboard";
import VueGoogleMaps from '@fawmi/vue-google-maps';

const appInstance = createApp(App);

appInstance.use(store);
appInstance.use(router);
appInstance.use(ArgonDashboard);
appInstance.use(VueGoogleMaps, {
  load: {
    key: 'AIzaSyBn5i4k6Emv3vQxozSkqce41--RjJoRUDc',
  },
});
appInstance.mount("#app");
