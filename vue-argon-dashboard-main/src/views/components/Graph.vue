<template>
    <div class="card">
      <div class="card-header pb-0">
        <h6>IP Count by Region</h6>
      </div>
      <div class="card-body">
        <img v-if="chartUrl" :src="chartUrl" alt="IP Count by Region Chart" />
        <p v-else>No data available to display the chart.</p>
      </div>
    </div>
  </template>
  
  <script>
  export default {
    props: {
      ips: Array
    },
    data() {
      return {
        chartUrl: ''
      };
    },
    mounted() {
      this.generateChartUrl();
    },
    methods: {
      generateChartUrl() {
        const regionCounts = {};
        this.ips.forEach(ip => {
          if (ip.region) {
            regionCounts[ip.region] = (regionCounts[ip.region] || 0) + 1;
          }
        });
  
        const labels = Object.keys(regionCounts);
        const data = Object.values(regionCounts);
  
        if (labels.length === 0 || data.length === 0) {
          this.chartUrl = '';
          return;
        }
  
        const chartConfig = {
          type: 'pie',
          data: {
            labels: labels,
            datasets: [{
              data: data,
              backgroundColor: [
                'rgb(255, 99, 132)',
                'rgb(255, 159, 64)',
                'rgb(255, 205, 86)',
                'rgb(75, 192, 192)',
                'rgb(54, 162, 235)',
                'rgb(153, 102, 255)',
                'rgb(201, 203, 207)'
              ]
            }]
          }
        };
  
        this.chartUrl = `https://quickchart.io/chart?c=${encodeURIComponent(JSON.stringify(chartConfig))}`;
      }
    }
  }
  </script>
  
  <style>
  .card-body img {
    width: 100%;
    height: auto;
  }
  </style>
  