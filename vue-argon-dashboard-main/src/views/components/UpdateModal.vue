<template>
  <div class="modal" v-if="show">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Update IP Address</h5>
        </div>
        <div class="modal-body">
          <input v-model="ipAddress" class="form-control" placeholder="Enter new IP Address" />
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" @click="close">Close</button>
          <button type="button" class="btn btn-primary" @click="update">Update</button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  props: {
    show: {
      type: Boolean,
      required: true
    },
    ip: {
      type: Object,
      default: () => ({})
    }
  },
  data() {
    return {
      ipAddress: this.ip ? this.ip.address : ''
    };
  },
  watch: {
    ip: {
      immediate: true,
      handler(newVal) {
        this.ipAddress = newVal ? newVal.address : '';
      }
    }
  },
  methods: {
    close() {
      this.$emit('close');
    },
    async update() {
      try {
        const response = await fetch(`/api/ips/${this.ip.id}`, {
          method: 'PUT',
          headers: {
            'Content-Type': 'application/json'
          },
          body: JSON.stringify({ address: this.ipAddress })
        });

        if (!response.ok) {
          const error = await response.json();
          throw new Error(error.error || 'Failed to update IP address');
        }

        const updatedIp = await response.json();
        this.$emit('update', updatedIp);
        this.close();
      } catch (error) {
        this.$emit('error', error.message);
      }
    }
  }
};
</script>

<style scoped>
.modal {
  display: block;
  background-color: rgba(0, 0, 0, 0.5);
}
</style>