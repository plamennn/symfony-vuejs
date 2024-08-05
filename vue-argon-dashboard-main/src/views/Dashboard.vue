<template>

  
  <div class="py-4 container-fluid">
    <div class="row">
      <div class="col-lg-12">
        <div class="row">
          <div class="col-lg-7 mb-lg">
            <!-- line chart -->
          </div>
          <div class="col-lg-5">
            <carousel />
          </div>
        </div>
        <div class="row mt-4">
          <div class="col-lg-6 mb-lg-4">
            <newest-ips :ips="ipAddresses" />
          </div>
          <div class="col-lg-6 mb-lg-4">
            <most-used-regions :ips="ipAddresses" />
          </div>
        </div>
        <div class="row mt-4">
          <div class="col-lg-6 mb-lg-4">
            <graph :ips="ipAddresses" />
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import Carousel from "./components/Carousel.vue";
import NewestIps from "./components/NewestIps.vue";
import MostUsedRegions from "./components/MostUsedRegions.vue";
import Graph from "./components/Graph.vue";

import { ref, onMounted } from 'vue';

const ipAddresses = ref([]);

async function fetchIpAddresses() {
  try {
    const response = await fetch('/api/ips');
    if (!response.ok) {
      const error = await response.json();
      throw new Error(error.error || 'Failed to fetch IP addresses');
    }
    const data = await response.json();
    console.log('Fetched IP Addresses:', data); // Add this line to log the data
    ipAddresses.value = data;
  } catch (error) {
    console.error(error);
  }
}

onMounted(() => {
  fetchIpAddresses();
});
</script>
