<template>
    <div class="card">
      <div class="card-header pb-0">
        <h6>Most Used Regions</h6>
      </div>
      <div class="card-body px-0 pt-0 pb-2">
        <ul class="list-group">
          <li class="list-group-item" v-for="(count, region) in regionCounts" :key="region">
            {{ region }} - {{ count }}
          </li>
        </ul>
      </div>
    </div>
  </template>
  
  <script>
  export default {
    props: {
      ips: Array
    },
    computed: {
      regionCounts() {
        const counts = {};
        this.ips.forEach(ip => {
          if (ip.region) {
            counts[ip.region] = (counts[ip.region] || 0) + 1;
          }
        });
        return counts;
      }
    }
  }
  </script>
  