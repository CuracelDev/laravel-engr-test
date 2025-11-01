<script setup>
import { ref, onMounted } from 'vue'
import axios from 'axios'
import GuestLayout from '../Layouts/GuestLayout.vue'
defineOptions({ layout: GuestLayout })

const loading = ref(false)
const error = ref('')
const date = ref('')
const insurers = ref([])
const batches = ref([])

async function loadInsurers() {
  const { data } = await axios.get('/api/insurers')
  insurers.value = data
}

async function loadBatches() {
  loading.value = true
  error.value = ''
  try {
    const param = date.value ? `?date=${date.value}` : '?date=yesterday'
    const { data } = await axios.get(`/api/batches${param}`)
    batches.value = data.batches
  } catch (e) {
    error.value = e.response?.data?.message || e.message
  } finally {
    loading.value = false
  }
}

onMounted(() => {
  loadInsurers()
  loadBatches()
})
</script>

<template>
  <div class="max-w-5xl mx-auto p-6">
    <h1 class="text-2xl font-semibold mb-4">Batches Overview</h1>

    <div class="flex gap-4 items-end mb-6">
      <div>
        <label class="block text-sm font-medium">Select Date</label>
        <input v-model="date" type="date" class="border rounded p-2" />
      </div>
      <button @click="loadBatches" class="px-4 py-2 bg-black text-white rounded">
        Load
      </button>
    </div>

    <div v-if="loading" class="text-gray-500">Loading...</div>
    <div v-if="error" class="text-red-600">{{ error }}</div>

    <table v-if="!loading && batches.length" class="w-full text-left border border-gray-200">
      <thead class="bg-gray-100">
        <tr>
          <th class="p-2 border">Batch Code</th>
          <th class="p-2 border">Insurer</th>
          <th class="p-2 border">Provider</th>
          <th class="p-2 border">Date</th>
          <th class="p-2 border">Claims Count</th>
          <th class="p-2 border">Total Amount</th>
          <th class="p-2 border">Status</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="b in batches" :key="b.id">
          <td class="p-2 border">{{ b.batch_code }}</td>
          <td class="p-2 border">{{ b.insurer?.code }}</td>
          <td class="p-2 border">{{ b.provider_name }}</td>
          <td class="p-2 border">{{ b.batch_date }}</td>
          <td class="p-2 border">{{ b.claims_count }}</td>
          <td class="p-2 border">{{ Number(b.total_amount).toLocaleString() }}</td>
          <td class="p-2 border">{{ b.status }}</td>
        </tr>
      </tbody>
    </table>

    <div v-else-if="!loading" class="text-gray-500 mt-4">No batches found.</div>
  </div>
</template>