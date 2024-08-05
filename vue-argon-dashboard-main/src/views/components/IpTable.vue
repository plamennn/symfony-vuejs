<template>
  <div class="card">
    <div class="card-header pb-0">
      <h6>Tabuľka IP Adries</h6>
    </div>
    <div class="card-body px-0 pt-0 pb-2">
      <!-- Error Modal -->
      <ErrorModal :show="showErrorModal" :message="errorMessage" @close="showErrorModal = false" />

      <!-- Update Modal -->
      <UpdateModal :show="showUpdateModal" :ip="selectedIp" @close="showUpdateModal = false" @update="handleUpdate" @error="handleError" />

      <!-- Table and other content -->
      <div class="table-responsive p-0">
        <table class="table align-items-center mb-0">
          <thead>
            <tr>
              <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">#</th>
              <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">IP Adresa</th>
              <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Mesto</th>
              <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Región</th>
              <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Krajina</th>
              <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Poloha</th>
              <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Akcia</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="(ip, index) in ipAddresses" :key="ip.id">
              <td>
                <p class="text-xs font-weight-bold mb-0">{{ index + 1 }}</p>
              </td>
              <td>
                <div class="d-flex px-2 py-1">
                  <div class="d-flex flex-column justify-content-center">
                    <h6 class="mb-0 text-sm">{{ ip.address }}</h6>
                  </div>
                </div>
              </td>
              <td>
                <p class="text-xs text-secondary mb-0">{{ ip.city || 'N/A' }}</p>
              </td>
              <td>
                <p class="text-xs text-secondary mb-0">{{ ip.region || 'N/A' }}</p>
              </td>
              <td>
                <p class="text-xs text-secondary mb-0">{{ ip.country || 'N/A' }}</p>
              </td>
              <td>
                <p class="text-xs text-secondary mb-0">{{ ip.loc || 'N/A' }}</p>
              </td>
              <td class="align-middle text-center">
                <button class="btn btn-sm btn-primary" @click="synchronize(ip.id)" style="margin-right: 4px">
                  <i class="fa fa-sync"></i> 
                </button>
                <button class="btn btn-sm btn-danger" @click="deleteIpAddress(ip.id)" style="margin-right: 4px">
                  <i class="fa fa-trash"></i> 
                </button>
                <button class="btn btn-sm btn-info" @click="showUpdate(ip)">
                  <i class="fa fa-edit"></i> 
                </button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
      <div class="d-flex p-3">
        <input v-model="newIpAddress" class="form-control w-50 mt-2 me-2" placeholder="Zadajte IP Adresu" />
        <button class="btn btn-success mt-2" @click="addIpAddress">
          <i class="fa fa-plus"></i> Pridať IP
        </button>
      </div>
    </div>
  </div>
</template>

<script>
import ErrorModal from './ErrorModal.vue'; // Import the modal component
import UpdateModal from './UpdateModal.vue';

export default {
  components: {
    ErrorModal,
    UpdateModal
  },
  data() {
    return {
      newIpAddress: '',
      ipAddresses: [],
      errorMessage: null,
      showErrorModal: false,
      showUpdateModal: false,
      selectedIp: null
    };
  },
  methods: {
    async fetchIpAddresses() {
      try {
        const response = await fetch('/api/ips');
        if (!response.ok) {
          const error = await response.json();
          throw new Error(error.error || 'Nepodarilo sa načítať IP adresy');
        }
        const data = await response.json();
        this.ipAddresses = data;
      } catch (error) {
        this.errorMessage = error.message;
        this.showErrorModal = true;
      }
    },
    async addIpAddress() {
      const ipData = { address: this.newIpAddress };
      try {
        const response = await fetch('/api/ips', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
          },
          body: JSON.stringify(ipData),
        });
        if (!response.ok) {
          const error = await response.json();
          throw new Error(error.error || 'Nepodarilo sa pridať IP adresu');
        }
        const data = await response.json();
        this.ipAddresses.push(data);
        this.newIpAddress = '';
        await this.synchronize(data.id); // Synchronizácia ihneď po pridaní
      } catch (error) {
        this.errorMessage = error.message;
        this.showErrorModal = true;
      }
    },
    async synchronize(id) {
      try {
        const response = await fetch(`/api/ips/${id}/synchronize`, {
          method: 'POST',
        });
        if (!response.ok) {
          const error = await response.json();
          throw new Error(error.error || 'Nepodarilo sa synchronizovať IP adresu');
        }
        const updatedIp = await response.json();
        const index = this.ipAddresses.findIndex(ip => ip.id === id);
        if (index !== -1) {
          this.ipAddresses[index] = updatedIp; // Priama aktualizácia položky
        }
      } catch (error) {
        this.errorMessage = error.message;
        this.showErrorModal = true;
      }
    },
    async deleteIpAddress(id) {
      try {
        const response = await fetch(`/api/ips/${id}`, {
          method: 'DELETE',
        });
        if (!response.ok) {
          const error = await response.json();
          throw new Error(error.error || 'Nepodarilo sa zmazať IP adresu');
        }
        this.ipAddresses = this.ipAddresses.filter(ip => ip.id !== id);
      } catch (error) {
        this.errorMessage = error.message;
        this.showErrorModal = true;
      }
    },
    showUpdate(ip) {
      this.selectedIp = ip;
      this.showUpdateModal = true;
    },
    handleUpdate(updatedIp) {
      const index = this.ipAddresses.findIndex(ip => ip.id === updatedIp.id);
      if (index !== -1) {
        this.ipAddresses[index] = updatedIp;
      }
    }
  },
  
  mounted() {
    this.fetchIpAddresses();
  }
}
</script>

<style scoped>
/* Modal Styles */
.modal {
  display: block;
  background-color: rgba(0, 0, 0, 0.5);
}
</style>
