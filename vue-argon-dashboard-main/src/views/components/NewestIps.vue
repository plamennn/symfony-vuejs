<template>
    <div class="card">
      <div class="card-header pb-0">
        <h6>Newest IP Addresses</h6>
      </div>
      <div class="card-body px-0 pt-0 pb-2">
        <ul class="list-group">
          <li class="list-group-item" v-for="ip in newestIps" :key="ip.id">
            {{ ip.address }} - {{ ip.city || 'N/A' }}
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
      newestIps() {
        return this.ips.slice(-5).reverse(); // Get the last 5 IPs
      }
    }
  }
  </script>
  